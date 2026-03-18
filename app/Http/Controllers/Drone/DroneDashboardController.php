<?php

namespace App\Http\Controllers\Drone;

use App\Http\Controllers\Controller;
use App\Models\Drone;
use App\Models\DroneMission;
use App\Models\FieldBoundary;
use Illuminate\Http\Request;

class DroneDashboardController extends Controller
{
    public function index(Request $request)
    {
        $totalDrones     = Drone::count();
        $activeDrones    = Drone::where('status', 'active')->count();
        $totalBoundaries = FieldBoundary::count();
        $totalMissions   = DroneMission::count();
        $totalAreaHa     = FieldBoundary::whereNotNull('area_ha')->sum('area_ha');

        $recentMissions = DroneMission::with(['boundary', 'drone'])
            ->orderByDesc('updated_at')
            ->limit(5)
            ->get();

        $missionsByStatus = DroneMission::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        return view('drone.dashboard', compact(
            'totalDrones',
            'activeDrones',
            'totalBoundaries',
            'totalMissions',
            'totalAreaHa',
            'recentMissions',
            'missionsByStatus',
        ));
    }
}
