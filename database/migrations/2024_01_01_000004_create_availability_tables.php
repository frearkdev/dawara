<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('availability', function (Blueprint $table) {
            $table->string('id', 36)->primary();
            $table->string('barber_id', 36);
            $table->foreign('barber_id')->references('id')->on('barbers')->cascadeOnDelete();
            $table->unsignedTinyInteger('day_of_week'); // 0=zondag … 6=zaterdag
            $table->time('start_time');
            $table->time('end_time');
            $table->boolean('active')->default(true);
            $table->timestamps();
            $table->unique(['barber_id', 'day_of_week']);
        });

        Schema::create('availability_exceptions', function (Blueprint $table) {
            $table->string('id', 36)->primary();
            $table->string('barber_id', 36);
            $table->foreign('barber_id')->references('id')->on('barbers')->cascadeOnDelete();
            $table->date('date');
            $table->boolean('is_day_off')->default(false);
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->string('reason')->nullable();
            $table->timestamps();
            $table->unique(['barber_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('availability_exceptions');
        Schema::dropIfExists('availability');
    }
};
