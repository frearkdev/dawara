<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('business_settings', function (Blueprint $table) {
            $table->string('key', 100)->primary();
            $table->text('value')->nullable();
            $table->string('type', 20)->default('string');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('business_settings');
    }
};
