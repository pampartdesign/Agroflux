<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Models\CatalogCategory;
use App\Models\Product;
use App\Models\QrCode;
use App\Services\QrService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $q = Product::query()
            ->with('category')
            ->withCount(['listings', 'batches'])
            ->orderByDesc('created_at');

        if ($search = $request->string('q')->toString()) {
            $q->where(function ($qq) use ($search) {
                $qq->where('default_name', 'like', '%'.$search.'%')
                   ->orWhere('sku', 'like', '%'.$search.'%');
            });
        }

        if ($cat = $request->integer('category_id')) {
            $q->where('category_id', $cat);
        }

        $products = $q->paginate(18)->withQueryString();

        $productIds = $products->getCollection()->pluck('id')->values();
        $productQrs = QrCode::query()
            ->where('qrable_type', Product::class)
            ->whereIn('qrable_id', $productIds)
            ->get()
            ->keyBy('qrable_id');

        $categories = CatalogCategory::query()
            ->whereNull('parent_id')
            ->orderBy('name')
            ->get();

        return view('core.products.index', [
            'products'    => $products,
            'productQrs'  => $productQrs,
            'categories'  => $categories,
        ]);
    }

    public function create()
    {
        $categories = CatalogCategory::query()
            ->whereNull('parent_id')
            ->orderBy('name')
            ->with(['children' => fn($q) => $q->orderBy('name')])
            ->get();

        return view('core.products.create', [
            'categories'     => $categories,
            'categoriesJson' => $this->categoriesJson($categories),
        ]);
    }

    public function store(ProductRequest $request, QrService $qr)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('products', 'public');
        }
        unset($data['image']);

        $product = Product::query()->create($data);
        $qr->ensureQr($product);

        return redirect()->route('core.products.index')
            ->with('success', "\"{$product->default_name}\" has been added to your catalog.");
    }

    public function edit(Product $product)
    {
        $categories = CatalogCategory::query()
            ->whereNull('parent_id')
            ->orderBy('name')
            ->with(['children' => fn($q) => $q->orderBy('name')])
            ->get();

        return view('core.products.edit', [
            'product'        => $product,
            'categories'     => $categories,
            'categoriesJson' => $this->categoriesJson($categories),
        ]);
    }

    public function update(ProductRequest $request, Product $product, QrService $qr)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            if ($product->image_path) {
                Storage::disk('public')->delete($product->image_path);
            }
            $data['image_path'] = $request->file('image')->store('products', 'public');
        }
        unset($data['image']);

        $product->update($data);
        $qr->ensureQr($product);

        return redirect()->route('core.products.index')
            ->with('success', "\"{$product->default_name}\" has been updated.");
    }

    public function destroy(Product $product)
    {
        // Block deletion if product has active marketplace listings
        $activeListings = $product->listings()->where('is_active', true)->count();
        if ($activeListings > 0) {
            return back()->with('error', "Cannot delete \"{$product->default_name}\" — it has {$activeListings} active listing(s) on the marketplace. Hide or delete those listings first.");
        }

        // Delete all listings (inactive ones are safe to remove)
        $product->listings()->delete();

        // Delete product image from storage
        if ($product->image_path) {
            Storage::disk('public')->delete($product->image_path);
        }

        // Delete QR code record
        QrCode::query()
            ->where('qrable_type', Product::class)
            ->where('qrable_id', $product->id)
            ->delete();

        $name = $product->default_name;
        $product->delete();

        return redirect()->route('core.products.index')
            ->with('success', "\"{$name}\" has been deleted.");
    }

    private function categoriesJson($categories): string
    {
        $map = [];
        foreach ($categories as $cat) {
            $map[$cat->id] = $cat->children->map(fn($c) => [
                'id'   => $c->id,
                'name' => $c->name,
            ])->values()->all();
        }
        return json_encode($map);
    }
}
