<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\AuthorizedSeller;
use Illuminate\Http\Request;

class AuthorizedSellersController extends Controller
{
    public function index(Request $request)
    {
        $query = AuthorizedSeller::with(['products' => fn ($q) => $q->limit(20)])
            ->where('is_active', true);

        if ($request->filled('category')) {
            $query->where('category', $request->input('category'));
        }

        if ($request->filled('search')) {
            $search = '%' . $request->input('search') . '%';
            $query->where('company_name', 'like', $search);
        }

        // Admin-controlled sort order first, then alphabetical fallback
        $query->orderByRaw('CASE WHEN sort_order = 0 THEN 1 ELSE 0 END')
              ->orderBy('sort_order')
              ->orderBy('company_name');

        $sellers = $query->paginate(24)->withQueryString();

        $categories = AuthorizedSeller::where('is_active', true)
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        return view('authorized-sellers.index', compact('sellers', 'categories'));
    }
}
