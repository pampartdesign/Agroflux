<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\QrCode;
use Illuminate\Http\Request;

class TracePublicController extends Controller
{
    public function show(string $token)
    {
        $qr = QrCode::query()->where('public_token', $token)->firstOrFail();

        if ($qr->qrable_type !== Batch::class) {
            abort(404);
        }

        $batch = Batch::query()
            ->withoutGlobalScopes()
            ->with(['product', 'events'])
            ->findOrFail($qr->qrable_id);

        // Public timeline shows batch + events only (no tenant private data)
        return view('public.trace', [
            'batch' => $batch,
        ]);
    }
}
