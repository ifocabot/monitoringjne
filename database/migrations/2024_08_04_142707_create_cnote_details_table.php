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
        Schema::create('cnote_details', function (Blueprint $table) {
            $table->id();
            $table->string('cnote_number', 50);
            $table->dateTime('cnote_date')->nullable();
            $table->string('cnote_origin', 50)->nullable();
            $table->decimal('cnote_weight', 10, 2)->nullable();
            $table->string('cnote_shipper_city', 100)->nullable();
            $table->string('cnote_shipper_name', 100)->nullable();
            $table->string('cnote_receiver_city', 100)->nullable();
            $table->string('cnote_receiver_name', 100)->nullable();
            $table->string('cnote_shipper_addr1', 255)->nullable();
            $table->string('cnote_shipper_addr2', 255)->nullable();
            $table->string('cnote_shipper_addr3', 255)->nullable();
            $table->string('cnote_receiver_addr1', 255)->nullable();
            $table->string('cnote_receiver_addr2', 255)->nullable();
            $table->string('cnote_receiver_addr3', 255)->nullable();
            $table->foreign('cnote_number')->references('cnote_number')->on('cnotes');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cnote_details');
    }
};
