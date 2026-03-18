<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sensors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('gateway_id')->nullable()->constrained('iot_gateways')->nullOnDelete();

            $table->string('group_key'); // irrigation, humidity, temperature, rfid, trough_level, barn_climate...
            $table->string('name');
            $table->string('identifier')->nullable(); // device id
            $table->string('unit')->nullable(); // %, C, cm, L...
            $table->string('status')->default('offline'); // online/offline
            $table->timestamps();

            $table->index(['tenant_id','group_key']);
            $table->index(['tenant_id','status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sensors');
    }
};
