<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('authorized_sellers', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');
            $table->string('category')->nullable();          // product category filter tag
            $table->text('short_description')->nullable();
            $table->string('featured_image')->nullable();    // relative path under storage/public
            $table->string('website_url')->nullable();
            $table->unsignedInteger('sort_order')->default(0); // admin-controlled publish order
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('authorized_sellers');
    }
};
