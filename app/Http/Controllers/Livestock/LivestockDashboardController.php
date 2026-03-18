<?php

namespace App\Http\Controllers\Livestock;

use App\Http\Controllers\Controller;
use App\Models\LivestockAnimal;
use App\Models\LivestockProduceLog;
use App\Models\LivestockRoutineCheck;
use Illuminate\Http\Request;

class LivestockDashboardController extends Controller
{
    public function index(Request $request)
    {
        $animals = LivestockAnimal::all();

        $totalAnimals  = $animals->count();
        $pregnantCount = $animals->where('status', 'pregnant')->count();
        $sickCount     = $animals->where('status', 'sick')->count();

        $produceTodayCount = LivestockProduceLog::whereDate('logged_at', today())->count();

        $checksToday     = LivestockRoutineCheck::whereDate('checked_at', today())->count();
        $alertsThisMonth = LivestockRoutineCheck::whereMonth('checked_at', now()->month)
            ->whereYear('checked_at', now()->year)
            ->whereIn('status', ['alert', 'critical'])
            ->count();

        $speciesBreakdown = $animals->groupBy('species')
            ->map(fn ($g) => $g->count())
            ->sortByDesc(fn ($c) => $c);

        $recentChecks = LivestockRoutineCheck::with('animal')
            ->orderBy('checked_at', 'desc')
            ->limit(5)
            ->get();

        return view('livestock.dashboard', compact(
            'totalAnimals', 'pregnantCount', 'sickCount',
            'produceTodayCount', 'checksToday', 'alertsThisMonth',
            'speciesBreakdown', 'recentChecks'
        ));
    }
}
