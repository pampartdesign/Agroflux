<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('delivery_requests', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('farm_id')->constrained('farms')->cascadeOnDelete();

            // draft is not visible to truckers.
            $table->string('status')->default('draft'); // draft|open|offered|accepted|completed|self_delivered|cancelled
            $table->string('delivery_mode')->default('marketplace'); // marketplace|self

            $table->string('pickup_address');
            $table->string('delivery_address')->nullable();

            $table->string('cargo_description')->nullable();
            $table->decimal('cargo_weight_kg', 10, 2)->nullable();

            $table->date('requested_date')->nullable();

            $table->foreignId('accepted_offer_id')->nullable()->constrained('delivery_offers')->nullOnDelete();

            $table->timestamps();

            $table->index(['tenant_id', 'farm_id']);
            $table->index(['status', 'delivery_mode']);
            $table->index('requested_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_requests');
    }
};
