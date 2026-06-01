<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->string('id', 36)->primary();
            $table->string('appointment_id', 36)->nullable();
            $table->string('customer_id', 36)->nullable();
            $table->string('barber_id', 36)->nullable();
            $table->foreign('appointment_id')->references('id')->on('appointments')->nullOnDelete();
            $table->foreign('customer_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('barber_id')->references('id')->on('barbers')->nullOnDelete();
            $table->unsignedTinyInteger('rating');
            $table->text('comment')->nullable();
            $table->boolean('visible')->default(true);
            $table->string('source', 20)->default('system'); // 'system' | 'google'
            $table->string('google_review_id', 100)->nullable()->unique();
            $table->string('google_author', 100)->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
            $table->unique('appointment_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
