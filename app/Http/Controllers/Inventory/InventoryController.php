<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\InventoryItem;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $category = $request->query('category');

        $query = InventoryItem::orderBy('name');
        if ($category) {
            $query->where('category', $category);
        }
        $items = $query->get();

        $allItems    = InventoryItem::get();
        $total       = $allItems->count();
        $lowStock    = $allItems->filter(fn($i) => $i->isLowStock())->count();
        $outOfStock  = $allItems->filter(fn($i) => $i->isOutOfStock())->count();
        $categories  = $allItems->pluck('category')->unique()->count();

        return view('inventory.index', compact(
            'items', 'total', 'lowStock', 'outOfStock', 'categories', 'category'
        ));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'       => ['required', 'string', 'max:200'],
            'category'   => ['required', 'string', 'max:50'],
            'quantity'   => ['required', 'numeric', 'min:0'],
            'unit'       => ['required', 'string', 'max:20'],
            'min_qty'    => ['nullable', 'numeric', 'min:0'],
            'supplier'   => ['nullable', 'string', 'max:200'],
            'expires_at' => ['nullable', 'date'],
            'notes'      => ['nullable', 'string', 'max:1000'],
        ]);

        InventoryItem::create($data);

        return redirect()->route('inventory.index')->with('success', 'Item added.');
    }

    public function update(Request $request, InventoryItem $inventoryItem)
    {
        $data = $request->validate([
            'name'       => ['required', 'string', 'max:200'],
            'category'   => ['required', 'string', 'max:50'],
            'quantity'   => ['required', 'numeric', 'min:0'],
            'unit'       => ['required', 'string', 'max:20'],
            'min_qty'    => ['nullable', 'numeric', 'min:0'],
            'supplier'   => ['nullable', 'string', 'max:200'],
            'expires_at' => ['nullable', 'date'],
            'notes'      => ['nullable', 'string', 'max:1000'],
        ]);

        $inventoryItem->update($data);

        return redirect()->route('inventory.index')->with('success', 'Item updated.');
    }

    public function destroy(InventoryItem $inventoryItem)
    {
        $inventoryItem->delete();

        return redirect()->route('inventory.index')->with('success', 'Item removed.');
    }
}
