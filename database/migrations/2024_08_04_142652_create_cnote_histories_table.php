<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void

    {
        Schema::create('cnote_histories', function (Blueprint $table) {
            $table->id();
            $table->string('cnote_number', 50);
            $table->string('code', 10);
            $table->dateTime('date');
            $table->text('description');
            $table->text('photo1')->nullable();
            $table->text('photo2')->nullable();
            $table->text('photo3')->nullable();
            $table->text('photo4')->nullable();
            $table->text('photo5')->nullable();
            $table->foreign('cnote_number')->references('cnote_number')->on('cnotes');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cnote_histories');
    }
};
