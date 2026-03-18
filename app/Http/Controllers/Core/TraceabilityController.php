<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use App\Http\Requests\BatchRequest;
use App\Http\Requests\TraceabilityEventRequest;
use App\Models\Batch;
use App\Models\Product;
use App\Models\QrCode;
use App\Models\TraceabilityEvent;
use App\Services\QrService;
use Illuminate\Http\Request;

class TraceabilityController extends Controller
{
    public function index(Request $request)
    {
        $q = Batch::query()->with('product')->orderByDesc('created_at');

        if ($status = $request->string('status')->toString()) {
            $q->where('status', $status);
        }

        return view('core.traceability.index', [
            'batches' => $q->paginate(15)->withQueryString(),
        ]);
    }

    public function createBatch()
    {
        return view('core.traceability.batch-create', [
            'products' => Product::query()->orderBy('default_name')->get(),
        ]);
    }

    public function storeBatch(BatchRequest $request, QrService $qr)
    {
        $batch = Batch::query()->create($request->validated());
        $qr->ensureQr($batch);

        return redirect()->route('core.traceability.index');
    }

    public function showBatch(Batch $batch)
    {
        $qr = QrCode::query()
            ->where('qrable_type', Batch::class)
            ->where('qrable_id', $batch->id)
            ->first();

        return view('core.traceability.batch-show', [
            'batch' => $batch->load(['product', 'events']),
            'qr' => $qr,
        ]);
    }

    public function addEvent(Batch $batch)
    {
        return view('core.traceability.event-create', [
            'batch' => $batch,
        ]);
    }

    public function storeEvent(TraceabilityEventRequest $request, Batch $batch)
    {
        TraceabilityEvent::query()->create([
            'tenant_id' => $batch->tenant_id,
            'batch_id' => $batch->id,
            'event_type' => $request->string('event_type')->toString(),
            'occurred_at' => $request->date('occurred_at'),
            'notes' => $request->input('notes'),
            'meta' => null,
        ]);

        return redirect()->route('core.traceability.batch.show', $batch);
    }
}
