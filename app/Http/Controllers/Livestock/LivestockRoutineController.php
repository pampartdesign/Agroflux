<?php

namespace App\Http\Controllers\Livestock;

use App\Http\Controllers\Controller;
use App\Models\LivestockAnimal;
use App\Models\LivestockRoutineCheck;
use App\Services\CurrentTenant;
use Illuminate\Http\Request;

class LivestockRoutineController extends Controller
{
    public function index(Request $request)
    {
        $checks  = LivestockRoutineCheck::with('animal')->orderBy('checked_at', 'desc')->get();
        $animals = LivestockAnimal::orderBy('tag')->get();

        $checksToday   = $checks->filter(fn ($c) => $c->checked_at->isToday())->count();
        $feedingLogs   = $checks->whereIn('type', ['Morning Feeding', 'Evening Feeding'])->count();
        $healthAlerts  = $checks->whereIn('status', ['alert', 'critical'])->count();
        $vetThisMonth  = $checks->where('type', 'Vet Visit')
            ->filter(fn ($c) => $c->checked_at->isCurrentMonth())->count();

        return view('livestock.routine.index', compact(
            'checks', 'animals', 'checksToday', 'feedingLogs', 'healthAlerts', 'vetThisMonth'
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
            'type'       => ['required', 'string', 'max:60'],
            'animal_id'  => ['nullable', 'integer', 'exists:livestock_animals,id'],
            'status'     => ['required', 'in:normal,alert,critical'],
            'checked_at' => ['required', 'date'],
            'notes'      => ['nullable', 'string', 'max:2000'],
        ]);

        $data['tenant_id'] = $tenantId;

        LivestockRoutineCheck::create($data);

        return back()->with('success', 'Check logged successfully.');
    }

    public function destroy(LivestockRoutineCheck $check)
    {
        $tenantId = app(CurrentTenant::class)->id();
        if ($tenantId) {
            abort_unless($check->tenant_id === $tenantId, 403);
        }

        $check->delete();

        return back()->with('success', 'Check removed.');
    }
}
