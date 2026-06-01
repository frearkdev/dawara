<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('barbers', function (Blueprint $table) {
            $table->string('id', 36)->primary();
            $table->string('user_id', 36);
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->text('bio')->nullable();
            $table->string('avatar')->nullable();
            $table->json('specialties')->nullable();
            $table->boolean('active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('barbers');
    }
};
