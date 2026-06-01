<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->string('id', 36)->primary();
            $table->string('name');
            $table->text('description')->nullable();
            $table->unsignedInteger('duration_minutes');
            $table->unsignedInteger('price_cents');
            $table->string('color', 7)->default('#B8860B');
            $table->boolean('active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('barber_service', function (Blueprint $table) {
            $table->string('barber_id', 36);
            $table->string('service_id', 36);
            $table->foreign('barber_id')->references('id')->on('barbers')->cascadeOnDelete();
            $table->foreign('service_id')->references('id')->on('services')->cascadeOnDelete();
            $table->primary(['barber_id', 'service_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('barber_service');
        Schema::dropIfExists('services');
    }
};
