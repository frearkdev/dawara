<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->string('id', 36)->primary();
            $table->string('appointment_id', 36);
            $table->foreign('appointment_id')->references('id')->on('appointments')->cascadeOnDelete();
            $table->string('mollie_payment_id')->nullable()->unique();
            $table->string('mollie_checkout_url')->nullable();
            $table->unsignedInteger('amount_cents');
            $table->string('currency', 3)->default('EUR');
            $table->string('status')->default('open');
            $table->string('method')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
