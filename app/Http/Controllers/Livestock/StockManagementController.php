<?php

namespace App\Http\Controllers\Livestock;

use App\Http\Controllers\Controller;
use App\Models\LivestockAnimal;
use App\Services\CurrentTenant;
use Illuminate\Http\Request;

class StockManagementController extends Controller
{
    public function index(Request $request)
    {
        $animals = LivestockAnimal::orderBy('species')->orderBy('tag')->get();

        $totalAnimals  = $animals->count();
        $pregnantCount = $animals->where('status', 'pregnant')->count();
        $vacsDue       = 0; // placeholder — extend with vaccination tracking later
        $newThisMonth  = $animals->filter(
            fn ($a) => $a->created_at->month === now()->month && $a->created_at->year === now()->year
        )->count();

        return view('livestock.stock.index', compact(
            'animals', 'totalAnimals', 'pregnantCount', 'vacsDue', 'newThisMonth'
        ));
    }

    public function store(Request $request)
    {
        $tenantId = app(CurrentTenant::class)->id();
        abort_unless($tenantId, 403);

        $data = $request->validate([
            'tag'     => ['required', 'string', 'max:60'],
            'species' => ['required', 'string', 'max:60'],
            'breed'   => ['nullable', 'string', 'max:100'],
            'gender'  => ['nullable', 'in:male,female'],
            'dob'     => ['nullable', 'date'],
            'status'  => ['required', 'in:active,pregnant,sick,sold'],
            'notes'   => ['nullable', 'string', 'max:2000'],
        ]);

        $data['tenant_id'] = $tenantId;

        LivestockAnimal::create($data);

        return back()->with('success', 'Animal registered successfully.');
    }

    public function update(Request $request, LivestockAnimal $animal)
    {
        $tenantId = app(CurrentTenant::class)->id();
        if ($tenantId) {
            abort_unless($animal->tenant_id === $tenantId, 403);
        }

        $data = $request->validate([
            'tag'     => ['required', 'string', 'max:60'],
            'species' => ['required', 'string', 'max:60'],
            'breed'   => ['nullable', 'string', 'max:100'],
            'gender'  => ['nullable', 'in:male,female'],
            'dob'     => ['nullable', 'date'],
            'status'  => ['required', 'in:active,pregnant,sick,sold'],
            'notes'   => ['nullable', 'string', 'max:2000'],
        ]);

        $animal->update($data);

        return back()->with('success', 'Animal updated successfully.');
    }

    public function destroy(LivestockAnimal $animal)
    {
        $tenantId = app(CurrentTenant::class)->id();
        if ($tenantId) {
            abort_unless($animal->tenant_id === $tenantId, 403);
        }

        $animal->delete();

        return back()->with('success', 'Animal removed.');
    }
}
