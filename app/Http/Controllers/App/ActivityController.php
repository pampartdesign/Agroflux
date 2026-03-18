<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Services\CurrentTenant;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityController extends Controller
{
    public function index(Request $request, CurrentTenant $currentTenant): View
    {
        $tenant = $currentTenant->model();

        $logs = ActivityLog::query()
            ->where('tenant_id', $tenant?->id)
            ->latest()
            ->paginate(25);

        return view('app.activity.index', [
            'logs' => $logs,
        ]);
    }
}
