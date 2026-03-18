<?php

namespace App\Http\Controllers\Livestock;

use App\Http\Controllers\Controller;
use App\Models\LivestockAnimal;
use App\Models\LivestockProduceLog;
use App\Services\CurrentTenant;
use Illuminate\Http\Request;

class ProduceManagementController extends Controller
{
    public function index(Request $request)
    {
        $logs    = LivestockProduceLog::with('animal')->orderBy('logged_at', 'desc')->get();
        $animals = LivestockAnimal::orderBy('tag')->get();

        $milkToday  = $logs->where('type', 'Milk')->filter(fn ($l) => $l->logged_at->isToday())->sum('quantity');
        $eggsToday  = $logs->where('type', 'Eggs')->filter(fn ($l) => $l->logged_at->isToday())->sum('quantity');
        $weekCount  = $logs->filter(fn ($l) => $l->logged_at->isCurrentWeek())->count();
        $avgDaily   = $logs->groupBy(fn ($l) => $l->logged_at->toDateString())
                           ->map(fn ($g) => $g->sum('quantity'))
                           ->avg();

        return view('livestock.produce.index', compact(
            'logs', 'animals', 'milkToday', 'eggsToday', 'weekCount', 'avgDaily'
        ));
    }

    public function store(Request $request)
    {
        $tenantId = app(CurrentTenant::class)->id();

        if (!$tenantId && $request->filled('animal_id')) {
            $animal   = LivestockAnimal::withoutGlobalScopes()->findOrFail($request->input('animal_id'));
            $tenantId = $animal->tenant_id;
        }

        abort_unless($tenantId, 403);

        $data = $request->validate([
            'type'      => ['required', 'string', 'max:60'],
            'animal_id' => ['nullable', 'integer', 'exists:livestock_animals,id'],
            'quantity'  => ['required', 'numeric', 'min:0'],
            'unit'      => ['required', 'string', 'max:30'],
            'logged_at' => ['required', 'date'],
            'notes'     => ['nullable', 'string', 'max:2000'],
        ]);

        $data['tenant_id'] = $tenantId;

        LivestockProduceLog::create($data);

        return back()->with('success', 'Produce entry logged.');
    }

    public function destroy(LivestockProduceLog $log)
    {
        $tenantId = app(CurrentTenant::class)->id();
        if ($tenantId) {
            abort_unless($log->tenant_id === $tenantId, 403);
        }

        $log->delete();

        return back()->with('success', 'Entry removed.');
    }
}
