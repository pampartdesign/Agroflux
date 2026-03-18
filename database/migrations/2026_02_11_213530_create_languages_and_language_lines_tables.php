<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('languages', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->unique(); // en, el, tr, fr...
            $table->string('name');              // English, Ελληνικά...
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });

        Schema::create('language_lines', function (Blueprint $table) {
            $table->id();
            $table->string('group')->default('app'); // app, core, plus, logi, auth...
            $table->string('key');                   // dashboard.title
            $table->text('en')->nullable();          // default source text (English)
            $table->text('el')->nullable();          // Greek
            $table->json('extra')->nullable();       // additional languages later
            $table->timestamps();

            $table->unique(['group', 'key']);
            $table->index(['group', 'key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('language_lines');
        Schema::dropIfExists('languages');
    }
};
