<?php

namespace Database\Seeders;

use App\Models\SensorRule;
use App\Models\Tenant;
use Illuminate\Database\Seeder;

class SensorRuleSeeder extends Seeder
{
    /**
     * Seeds 3 demo Sensor Rules for the first tenant.
     *
     * Uses real sensor IDs from the existing sensors table:
     *   1 - Barn Temp. Sensor      [°C]
     *   2 - Field 3 irrigation     [Lt.]
     *   3 - Humidity sensor        [%]
     *   4 - water trough level     [Lt.]
     */
    public function run(): void
    {
        $tenant = Tenant::first();

        if (!$tenant) {
            $this->command->warn('No tenants found — skipping SensorRuleSeeder.');
            return;
        }

        // Remove old demo rules to keep it idempotent
        SensorRule::withoutGlobalScopes()
            ->where('tenant_id', $tenant->id)
            ->whereIn('name', [
                'Morning Irrigation Guard',
                'Low Field Humidity Alert',
                'Water Trough Level Monitor',
            ])
            ->delete();

        $rules = [
            // ── Rule 1: Time trigger + weather override ────────────────────────
            [
                'tenant_id'                 => $tenant->id,
                'name'                      => 'Morning Irrigation Guard',
                'description'               => 'Starts irrigation at 14:00 daily, but skips when significant rain is forecast. Waits 2 hours before retrying.',
                'is_active'                 => true,
                // Trigger: every day at 14:00
                'trigger_type'              => 'time',
                'trigger_time'              => '14:00',
                'trigger_sensor_id'         => null,
                'trigger_operator'          => null,
                'trigger_threshold'         => null,
                // Weather condition: skip if rain probability ≥ 40 %
                'weather_condition_enabled' => true,
                'weather_rain_skip_pct'     => 40,
                // Sensor condition: also skip if irrigation sensor already above 200 Lt.
                'sensor_condition_enabled'  => true,
                'condition_sensor_id'       => 2,   // Field 3 irrigation [Lt.]
                'condition_operator'        => 'gte',
                'condition_threshold'       => 200,
                // Action: log + notify
                'action_type'               => 'both',
                'action_notes'              => 'Check pump pressure after irrigation starts.',
                // Retry: wait 120 min, then fall back to next scheduled run
                'retry_type'                => 'both',
                'retry_wait_minutes'        => 120,
            ],

            // ── Rule 2: Sensor threshold trigger + notify ──────────────────────
            [
                'tenant_id'                 => $tenant->id,
                'name'                      => 'Low Field Humidity Alert',
                'description'               => 'Fires when field humidity drops below 30 %. Skips if it is already raining (forecast ≥ 60 %).',
                'is_active'                 => true,
                // Trigger: when humidity sensor < 30 %
                'trigger_type'              => 'sensor_threshold',
                'trigger_time'              => null,
                'trigger_sensor_id'         => 3,   // Humidity sensor [%]
                'trigger_operator'          => 'lt',
                'trigger_threshold'         => 30,
                // Weather condition: skip if heavy rain already forecast
                'weather_condition_enabled' => true,
                'weather_rain_skip_pct'     => 60,
                // No additional sensor condition
                'sensor_condition_enabled'  => false,
                'condition_sensor_id'       => null,
                'condition_operator'        => null,
                'condition_threshold'       => null,
                // Action: notify only
                'action_type'               => 'notify',
                'action_notes'              => 'Consider manual irrigation or check drip lines.',
                // Retry: next scheduled evaluation
                'retry_type'                => 'next_scheduled',
                'retry_wait_minutes'        => null,
            ],

            // ── Rule 3: Sensor threshold trigger + log ─────────────────────────
            [
                'tenant_id'                 => $tenant->id,
                'name'                      => 'Water Trough Level Monitor',
                'description'               => 'Logs a warning when the water trough drops below 100 Lt. so staff can refill in time.',
                'is_active'                 => true,
                // Trigger: when trough level < 100 Lt.
                'trigger_type'              => 'sensor_threshold',
                'trigger_time'              => null,
                'trigger_sensor_id'         => 4,   // water trough level [Lt.]
                'trigger_operator'          => 'lt',
                'trigger_threshold'         => 100,
                // No weather condition
                'weather_condition_enabled' => false,
                'weather_rain_skip_pct'     => null,
                // No sensor condition
                'sensor_condition_enabled'  => false,
                'condition_sensor_id'       => null,
                'condition_operator'        => null,
                'condition_threshold'       => null,
                // Action: log only
                'action_type'              => 'log',
                'action_notes'             => 'Refill trough before evening feeding.',
                // Retry: wait 30 min then try again
                'retry_type'               => 'wait_window',
                'retry_wait_minutes'       => 30,
            ],
        ];

        foreach ($rules as $data) {
            SensorRule::withoutGlobalScopes()->create($data);
        }

        $this->command->info("Seeded 3 sensor rules for tenant \"{$tenant->name}\".");
    }
}
