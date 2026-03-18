<?php

namespace App\Console\Commands;

use App\Models\Alert;
use App\Models\Sensor;
use App\Models\SensorReading;
use App\Models\SensorRule;
use App\Models\SensorRuleLog;
use App\Models\Tenant;
use App\Services\TomorrowWeatherService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * EvaluateSensorRules
 * ─────────────────────────────────────────────────────────────────────────────
 * Run every minute via the scheduler.
 * For each active SensorRule it:
 *   1. Checks whether the TRIGGER fires (time match or sensor threshold crossed).
 *   2. Checks OVERRIDE CONDITIONS (weather forecast, live sensor reading).
 *   3. If conditions hold → records a "skipped" or "waiting_retry" log entry
 *      and schedules a retry window if configured.
 *   4. If conditions pass (or retry window expired) → executes the ACTION:
 *        - Creates an Alert (in-app notification visible on the IoT dashboard)
 *        - Writes a "executed" / "retried" log entry
 *   5. Updates last_triggered_at / last_status / next_retry_at on the rule.
 */
class EvaluateSensorRules extends Command
{
    protected $signature   = 'sensor-rules:evaluate {--summary : Print a summary table of all rules and their last status}';
    protected $description = 'Evaluate all active sensor rules and fire actions when conditions are met';

    public function __construct(private readonly TomorrowWeatherService $weather)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $now = now();
        $this->line("[{$now->toDateTimeString()}] Evaluating sensor rules…");

        $rules = SensorRule::with(['triggerSensor', 'conditionSensor', 'tenant'])
            ->where('is_active', true)
            ->get();

        // --summary: print a diagnostic table of ALL rules (active or not)
        if ($this->option('summary')) {
            $all = SensorRule::withoutGlobalScopes()->with('tenant')->get();
            if ($all->isEmpty()) {
                $this->warn('No sensor rules exist in the database.');
                $this->line('  → Go to /plus/iot/rules and click "+ Add Condition" to create your first rule.');
                $this->line('  → Or run: php artisan db:seed --class=SensorRuleSeeder');
                return self::SUCCESS;
            }
            $this->table(
                ['ID', 'Name', 'Tenant', 'Active', 'Trigger', 'Last Status', 'Last Run'],
                $all->map(fn($r) => [
                    $r->id,
                    $r->name,
                    $r->tenant?->name ?? '—',
                    $r->is_active ? '✅ yes' : '❌ no',
                    $r->triggerLabel(),
                    $r->last_status ?? 'never run',
                    $r->last_triggered_at?->diffForHumans() ?? '—',
                ])->toArray()
            );
            return self::SUCCESS;
        }

        if ($rules->isEmpty()) {
            $total = SensorRule::withoutGlobalScopes()->count();
            if ($total === 0) {
                $this->warn('No sensor rules exist yet.');
                $this->line('  → Create rules at: /plus/iot/rules');
                $this->line('  → Or seed demo rules: php artisan db:seed --class=SensorRuleSeeder');
            } else {
                $this->warn("Found {$total} rule(s) in total, but none are active.");
                $this->line('  → Enable rules at: /plus/iot/rules (toggle the "Enable" button)');
                $this->line('  → Or run with --summary to see all rules: php artisan sensor-rules:evaluate --summary');
            }
            return self::SUCCESS;
        }

        foreach ($rules as $rule) {
            try {
                $this->evaluateRule($rule, $now);
            } catch (Throwable $e) {
                Log::error("SensorRule #{$rule->id} evaluation failed", [
                    'rule'    => $rule->name,
                    'error'   => $e->getMessage(),
                ]);
                $this->error("  Rule #{$rule->id} \"{$rule->name}\": {$e->getMessage()}");
            }
        }

        $this->info('Done.');
        return self::SUCCESS;
    }

    // ── Core evaluation ───────────────────────────────────────────────────────

    private function evaluateRule(SensorRule $rule, Carbon $now): void
    {
        $context = [
            'trigger_type'       => $rule->trigger_type,
            'trigger_fired'      => false,
            'weather_prob'       => null,
            'sensor_value'       => null,
            'condition_blocked'  => false,
            'retry_scheduled_at' => null,
            'alert_created'      => false,
        ];

        // ── 1. Check TRIGGER ──────────────────────────────────────────────────
        $triggerFired = false;

        if ($rule->trigger_type === 'time') {
            $triggerFired = $this->checkTimeTrigger($rule, $now);
        } elseif ($rule->trigger_type === 'sensor_threshold') {
            [$triggerFired, $context['sensor_value']] = $this->checkSensorTrigger($rule);
        }

        // Check if we are inside a retry window (retry already scheduled, window not expired)
        $inRetryWindow = $rule->next_retry_at && $now->lt($rule->next_retry_at);

        if (!$triggerFired && !$inRetryWindow) {
            // Nothing to do — trigger hasn't fired and not in retry
            return;
        }

        $context['trigger_fired'] = true;
        $isRetry = !$triggerFired && $inRetryWindow;

        // ── 2. Check OVERRIDE CONDITIONS ──────────────────────────────────────
        [$blocked, $blockReason, $context] = $this->checkConditions($rule, $context);

        if ($blocked) {
            $context['condition_blocked'] = true;

            // Determine retry behaviour
            $retryStatus    = $blockReason === 'weather' ? 'skipped_weather' : 'skipped_sensor';
            $nextRetryAt    = null;

            if (in_array($rule->retry_type, ['wait_window', 'both'], true) && $rule->retry_wait_minutes) {
                // Only schedule a retry if we haven't already scheduled one
                if (!$rule->next_retry_at || $now->gte($rule->next_retry_at)) {
                    $nextRetryAt = $now->copy()->addMinutes($rule->retry_wait_minutes);
                    $context['retry_scheduled_at'] = $nextRetryAt->toDateTimeString();
                    $retryStatus = 'waiting_retry';
                }
            }

            $sensorName = $rule->conditionSensor?->name ?? 'unknown sensor';
            $summary = $blockReason === 'weather'
                ? "Rule held: rain probability {$context['weather_prob']}% >= threshold {$rule->weather_rain_skip_pct}%."
                : "Rule held: sensor \"{$sensorName}\" value {$context['sensor_value']} matched condition.";

            if ($nextRetryAt) {
                $summary .= " Retry scheduled at {$nextRetryAt->format('H:i')}.";
            }

            $this->writeLog($rule, $retryStatus, $summary, $context);

            $rule->update([
                'last_triggered_at' => $now,
                'last_status'       => $retryStatus,
                'next_retry_at'     => $nextRetryAt,
            ]);

            $this->line("  #{$rule->id} \"{$rule->name}\" → {$retryStatus}");
            return;
        }

        // ── 3. CONDITIONS PASS → EXECUTE ACTION ──────────────────────────────
        $status  = $isRetry ? 'retried' : 'executed';
        $summary = $isRetry
            ? "Rule retried after wait window: conditions cleared. Action: {$rule->action_type}."
            : "Rule triggered successfully. Action: {$rule->action_type}.";

        if ($rule->trigger_type === 'time') {
            $summary = "Scheduled trigger at {$rule->trigger_time} fired. " . $summary;
        } else {
            $summary = "Sensor threshold crossed (value: {$context['sensor_value']}). " . $summary;
        }

        if ($rule->action_notes) {
            $summary .= " Notes: {$rule->action_notes}";
        }

        $this->executeAction($rule, $summary, $context, $now);
        $context['alert_created'] = true;

        $this->writeLog($rule, $status, $summary, $context);

        $rule->update([
            'last_triggered_at' => $now,
            'last_status'       => $status,
            'next_retry_at'     => null,   // clear any pending retry
        ]);

        $this->line("  #{$rule->id} \"{$rule->name}\" → {$status}");
    }

    // ── Trigger checks ────────────────────────────────────────────────────────

    /**
     * Returns true if the current minute matches the rule's trigger_time.
     * We allow a ±1 min window to handle scheduler drift.
     */
    private function checkTimeTrigger(SensorRule $rule, Carbon $now): bool
    {
        if (!$rule->trigger_time) {
            return false;
        }

        [$h, $m]   = explode(':', $rule->trigger_time);
        $targetMin = Carbon::today()->setHour((int)$h)->setMinute((int)$m);

        return abs($now->diffInMinutes($targetMin, false)) <= 1
            && $now->toDateString() !== $rule->last_triggered_at?->toDateString();
    }

    /**
     * Returns [bool $fired, float|null $latestValue].
     */
    private function checkSensorTrigger(SensorRule $rule): array
    {
        if (!$rule->trigger_sensor_id || !$rule->trigger_operator) {
            return [false, null];
        }

        $reading = SensorReading::query()
            ->where('sensor_id', $rule->trigger_sensor_id)
            ->orderByDesc('recorded_at')
            ->value('value');

        if ($reading === null) {
            return [false, null];
        }

        $value = (float) $reading;
        $fired = $this->compare($value, $rule->trigger_operator, (float) $rule->trigger_threshold);

        return [$fired, $value];
    }

    // ── Condition checks ──────────────────────────────────────────────────────

    /**
     * Returns [bool $blocked, string $reason, array $context].
     */
    private function checkConditions(SensorRule $rule, array $context): array
    {
        // ── Weather condition ─────────────────────────────────────────────────
        if ($rule->weather_condition_enabled && $rule->weather_rain_skip_pct !== null) {
            $prob = $this->fetchRainProbability($rule->tenant);
            $context['weather_prob'] = $prob;

            if ($prob !== null && $prob >= $rule->weather_rain_skip_pct) {
                return [true, 'weather', $context];
            }
        }

        // ── Sensor condition ──────────────────────────────────────────────────
        if ($rule->sensor_condition_enabled && $rule->condition_sensor_id && $rule->condition_operator) {
            $reading = SensorReading::query()
                ->where('sensor_id', $rule->condition_sensor_id)
                ->orderByDesc('recorded_at')
                ->value('value');

            if ($reading !== null) {
                $value                 = (float) $reading;
                $context['sensor_value'] = $value;

                if ($this->compare($value, $rule->condition_operator, (float) $rule->condition_threshold)) {
                    return [true, 'sensor', $context];
                }
            }
        }

        return [false, '', $context];
    }

    // ── Action execution ──────────────────────────────────────────────────────

    private function executeAction(SensorRule $rule, string $summary, array $context, Carbon $now): void
    {
        if (!in_array($rule->action_type, ['notify', 'both'], true)) {
            return; // log-only — no Alert needed
        }

        // Build alert title
        $title = "Rule fired: {$rule->name}";

        $message = $summary;
        if ($context['weather_prob'] !== null) {
            $message .= " (Rain probability: {$context['weather_prob']}%)";
        }

        Alert::create([
            'tenant_id'    => $rule->tenant_id,
            'sensor_id'    => $rule->trigger_sensor_id ?? $rule->condition_sensor_id,
            'severity'     => 'info',
            'title'        => $title,
            'message'      => $message,
            'triggered_at' => $now,
        ]);
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function fetchRainProbability(?Tenant $tenant): ?int
    {
        if (!$tenant) {
            return null;
        }

        try {
            if ($tenant->lat && $tenant->lng) {
                $forecast = $this->weather->forecast($tenant->lat, $tenant->lng);
            } else {
                return null;
            }

            return data_get($forecast, 'current.precip_prob');
        } catch (Throwable $e) {
            Log::warning('EvaluateSensorRules: could not fetch weather', ['error' => $e->getMessage()]);
            return null;
        }
    }

    private function compare(float $value, string $operator, float $threshold): bool
    {
        return match($operator) {
            'lt'  => $value < $threshold,
            'lte' => $value <= $threshold,
            'gt'  => $value > $threshold,
            'gte' => $value >= $threshold,
            'eq'  => abs($value - $threshold) < 0.001,
            default => false,
        };
    }

    private function writeLog(SensorRule $rule, string $status, string $summary, array $context): void
    {
        SensorRuleLog::create([
            'sensor_rule_id' => $rule->id,
            'tenant_id'      => $rule->tenant_id,
            'status'         => $status,
            'summary'        => $summary,
            'context'        => $context,
            'evaluated_at'   => now(),
        ]);
    }
}
