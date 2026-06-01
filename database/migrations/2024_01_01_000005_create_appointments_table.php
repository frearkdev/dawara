<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->string('id', 36)->primary();
            $table->string('customer_id', 36);
            $table->string('barber_id', 36);
            $table->string('service_id', 36);
            $table->foreign('customer_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('barber_id')->references('id')->on('barbers')->cascadeOnDelete();
            $table->foreign('service_id')->references('id')->on('services')->cascadeOnDelete();
            $table->dateTime('starts_at');
            $table->dateTime('ends_at');
            $table->enum('status', ['pending', 'confirmed', 'completed', 'cancelled', 'no_show'])->default('pending');
            $table->unsignedInteger('price_cents');
            $table->text('notes')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->string('cancelled_by', 36)->nullable();
            $table->foreign('cancelled_by')->references('id')->on('users')->nullOnDelete();
            $table->timestamp('reminder_sent_at')->nullable();
            $table->enum('source', ['online', 'admin', 'walkin'])->default('online');
            $table->timestamps();
            $table->softDeletes();
            $table->index(['barber_id', 'starts_at']);
            $table->index(['customer_id', 'starts_at']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
