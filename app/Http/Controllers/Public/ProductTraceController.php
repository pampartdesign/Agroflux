<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\Product;
use App\Models\QrCode;

class ProductTraceController extends Controller
{
    public function show(string $token)
    {
        $qr = QrCode::query()->where('public_token', $token)->firstOrFail();

        if ($qr->qrable_type !== Product::class) {
            abort(404);
        }

        $product = Product::query()
            ->withoutGlobalScopes()
            ->with(['category', 'subcategory'])
            ->findOrFail($qr->qrable_id);

        $publishedBatches = Batch::query()
            ->withoutGlobalScopes()
            ->with('events')                 // eager-load events for inline timeline
            ->where('product_id', $product->id)
            ->where('status', 'published')
            ->orderByDesc('created_at')
            ->get();

        $batchTokens = QrCode::query()
            ->where('qrable_type', Batch::class)
            ->whereIn('qrable_id', $publishedBatches->pluck('id'))
            ->get()
            ->keyBy('qrable_id');

        // If there is exactly one published batch with a token, jump straight to the timeline.
        if ($publishedBatches->count() === 1) {
            $only = $publishedBatches->first();
            $onlyToken = $batchTokens[$only->id] ?? null;
            if ($onlyToken) {
                return redirect()->route('public.trace', $onlyToken->public_token);
            }
        }

        return view('public.trace-product', [
            'product'     => $product,
            'batches'     => $publishedBatches,
            'batchTokens' => $batchTokens,
        ]);
    }
}
