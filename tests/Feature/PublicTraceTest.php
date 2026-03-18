<?php

namespace Tests\Feature;

use App\Models\Batch;
use App\Models\Product;
use App\Models\QrCode;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class PublicTraceTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_trace_page_loads_for_batch_token(): void
    {
        $tenant = Tenant::query()->create(['name' => 'T', 'trial_ends_at' => now()->addDays(14)]);
        $product = Product::query()->create(['tenant_id' => $tenant->id, 'default_name' => 'Olives']);
        $batch = Batch::query()->create(['tenant_id' => $tenant->id, 'product_id' => $product->id, 'code' => 'LOT-1', 'status' => 'published']);

        $token = (string) Str::uuid();
        QrCode::query()->create(['qrable_type' => Batch::class, 'qrable_id' => $batch->id, 'public_token' => $token, 'activated_at' => now()]);

        $this->get(route('public.trace', $token))->assertOk();
    }
}
