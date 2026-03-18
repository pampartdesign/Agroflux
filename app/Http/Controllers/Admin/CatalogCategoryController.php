<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CatalogCategory;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CatalogCategoryController extends Controller
{
    public function index()
    {
        $categories = CatalogCategory::query()
            ->whereNull('parent_id')
            ->withCount(['children', 'products', 'subProducts'])
            ->with(['children' => fn($q) => $q->withCount(['products', 'subProducts'])->orderBy('name')])
            ->orderBy('name')
            ->get();

        return view('admin.categories.index', compact('categories'));
    }

    public function create(Request $request)
    {
        // Pre-select parent when coming from "+ Sub-category" button on index
        $parentId = $request->integer('parent_id') ?: null;
        $parents  = CatalogCategory::whereNull('parent_id')->orderBy('name')->get();

        return view('admin.categories.create', compact('parents', 'parentId'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'      => ['required', 'string', 'max:100'],
            'parent_id' => ['nullable', 'integer', 'exists:catalog_categories,id'],
        ]);

        // Enforce max 2 levels (category → sub-category only)
        if (!empty($data['parent_id'])) {
            $parent = CatalogCategory::find($data['parent_id']);
            if ($parent && $parent->parent_id !== null) {
                return back()
                    ->withErrors(['parent_id' => 'Sub-categories cannot be nested further than one level.'])
                    ->withInput();
            }
        }

        $data['slug'] = $this->uniqueSlug($data['name']);

        CatalogCategory::create($data);

        return redirect()->route('admin.categories.index')
            ->with('status', 'Category created successfully.');
    }

    public function edit(CatalogCategory $category)
    {
        // Only top-level categories are valid parents; exclude self to avoid circular reference
        $parents = CatalogCategory::whereNull('parent_id')
            ->where('id', '!=', $category->id)
            ->orderBy('name')
            ->get();

        return view('admin.categories.edit', compact('category', 'parents'));
    }

    public function update(Request $request, CatalogCategory $category)
    {
        $data = $request->validate([
            'name'      => ['required', 'string', 'max:100'],
            'parent_id' => ['nullable', 'integer', 'exists:catalog_categories,id'],
        ]);

        // Prevent self-parenting
        if (!empty($data['parent_id']) && (int) $data['parent_id'] === $category->id) {
            return back()
                ->withErrors(['parent_id' => 'A category cannot be its own parent.'])
                ->withInput();
        }

        // Enforce max 2 levels
        if (!empty($data['parent_id'])) {
            $parent = CatalogCategory::find($data['parent_id']);
            if ($parent && $parent->parent_id !== null) {
                return back()
                    ->withErrors(['parent_id' => 'Sub-categories cannot be nested further than one level.'])
                    ->withInput();
            }
        }

        // Regenerate slug only when the name changes
        if ($data['name'] !== $category->name) {
            $data['slug'] = $this->uniqueSlug($data['name'], $category->id);
        }

        $category->update($data);

        return redirect()->route('admin.categories.index')
            ->with('status', '"' . $category->name . '" updated successfully.');
    }

    public function destroy(CatalogCategory $category)
    {
        // Block if products are directly linked to this category or sub-category
        $usedAsCategory    = Product::where('category_id', $category->id)->count();
        $usedAsSubcategory = Product::where('subcategory_id', $category->id)->count();
        $directUse         = $usedAsCategory + $usedAsSubcategory;

        if ($directUse > 0) {
            return back()->with('error',
                'Cannot delete — ' . $directUse . ' product(s) are currently assigned to "' . $category->name . '".'
            );
        }

        // Block if sub-categories have products
        if ($category->children()->exists()) {
            $childIds          = $category->children()->pluck('id');
            $childProductCount = Product::whereIn('category_id', $childIds)
                ->orWhereIn('subcategory_id', $childIds)
                ->count();

            if ($childProductCount > 0) {
                return back()->with('error',
                    'Cannot delete — sub-categories of "' . $category->name . '" contain ' . $childProductCount . ' product(s).'
                );
            }

            // Safe to cascade-delete orphaned sub-categories
            $category->children()->delete();
        }

        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('status', "Category deleted.");
    }

    // ── Helpers ────────────────────────────────────────────────────────────────

    private function uniqueSlug(string $name, ?int $exceptId = null): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $i    = 2;

        while (
            CatalogCategory::where('slug', $slug)
                ->when($exceptId, fn($q) => $q->where('id', '!=', $exceptId))
                ->exists()
        ) {
            $slug = $base . '-' . $i++;
        }

        return $slug;
    }
}
