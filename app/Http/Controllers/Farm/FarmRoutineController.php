<?php

namespace App\Http\Controllers\Farm;

use App\Http\Controllers\Controller;
use App\Models\Farm;
use App\Models\FarmRoutineTask;
use App\Models\Field;
use App\Services\CurrentTenant;
use Illuminate\Http\Request;

class FarmRoutineController extends Controller
{
    public function index(Request $request)
    {
        $fields = Field::with('farm')->orderBy('name')->get();
        $tasks  = FarmRoutineTask::with('field')
            ->orderBy('scheduled_at', 'desc')
            ->get();

        // Category counts (pending tasks)
        $categoryCounts = [
            'Irrigation'    => $tasks->where('type', 'Irrigation')->where('status', 'pending')->count(),
            'Fertilisation' => $tasks->where('type', 'Fertilisation')->where('status', 'pending')->count(),
            'Pest Control'  => $tasks->where('type', 'Pest Control')->where('status', 'pending')->count(),
            'Soil Check'    => $tasks->where('type', 'Soil Check')->where('status', 'pending')->count(),
        ];

        return view('farm.routine.index', compact('fields', 'tasks', 'categoryCounts'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'type'         => ['required', 'string', 'max:60'],
            'field_id'     => ['nullable', 'integer', 'exists:fields,id'],
            'scheduled_at' => ['required', 'date'],
            'notes'        => ['nullable', 'string', 'max:2000'],
        ]);

        $data['status'] = 'pending';

        // Derive tenant_id
        $tenantId = app(CurrentTenant::class)->id();

        if (!$tenantId && !empty($data['field_id'])) {
            $field    = Field::withoutGlobalScopes()->findOrFail($data['field_id']);
            $tenantId = $field->tenant_id;
        }

        abort_unless($tenantId, 403);
        $data['tenant_id'] = $tenantId;

        FarmRoutineTask::create($data);

        return back()->with('success', 'Task logged successfully.');
    }

    public function markDone(FarmRoutineTask $task)
    {
        $tenantId = app(CurrentTenant::class)->id();
        if ($tenantId) {
            abort_unless($task->tenant_id === $tenantId, 403);
        }

        $task->update(['status' => 'done', 'completed_at' => now()]);

        return back()->with('success', 'Task marked as done.');
    }

    public function destroy(FarmRoutineTask $task)
    {
        $tenantId = app(CurrentTenant::class)->id();
        if ($tenantId) {
            abort_unless($task->tenant_id === $tenantId, 403);
        }

        $task->delete();

        return back()->with('success', 'Task removed.');
    }
}
