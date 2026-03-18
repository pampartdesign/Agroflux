<?php

namespace App\Http\Controllers\Farm;

use App\Http\Controllers\Controller;
use App\Models\CropType;
use App\Models\Farm;
use App\Models\Field;
use Illuminate\Http\Request;

class FarmDashboardController extends Controller
{
    public function index(Request $request)
    {
        $farms     = Farm::withCount('fields')->orderBy('name')->get();
        $fields    = Field::with('farm', 'cropType.crop')->get();
        $cropTypes = CropType::with('crop')->get();

        $totalFarms       = $farms->count();
        $totalFields      = $fields->count();
        $totalCropTypes   = $cropTypes->count();
        $activeFields     = $fields->where('status', 'active')->count();
        $totalHectares    = (float) $fields->sum('area_ha');

        $harvestsThisYear = $fields->where('status', 'harvested')
            ->filter(fn ($f) => $f->harvest_at && $f->harvest_at->year === now()->year)
            ->count();

        $upcomingHarvests = $fields->where('status', 'active')
            ->filter(fn ($f) => $f->harvest_at
                && $f->harvest_at->isFuture()
                && $f->harvest_at->diffInDays(now()) <= 30)
            ->count();

        $overdueHarvests = $fields->where('status', 'active')
            ->filter(fn ($f) => $f->harvest_at && $f->harvest_at->isPast())
            ->count();

        // Crop breakdown — prefer the linked crop profile name, fall back to denormalised string
        $cropBreakdown = $fields
            ->filter(fn ($f) => ($f->cropType?->crop?->name ?? $f->cropType?->name ?? $f->crop_type))
            ->groupBy(fn ($f) => $f->cropType?->crop?->name ?? $f->cropType?->name ?? $f->crop_type)
            ->map(fn ($g) => $g->count())
            ->sortByDesc(fn ($v) => $v)
            ->take(6);

        return view('farm.dashboard', compact(
            'farms', 'fields', 'cropTypes',
            'totalFarms', 'totalFields', 'totalCropTypes',
            'activeFields', 'totalHectares',
            'harvestsThisYear', 'upcomingHarvests', 'overdueHarvests',
            'cropBreakdown'
        ));
    }
}
