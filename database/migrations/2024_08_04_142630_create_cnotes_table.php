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
        Schema::create('cnotes', function (Blueprint $table) {
            $table->id();
            $table->string('latitude', 20)->nullable();
            $table->string('longitude', 20)->nullable();
            $table->text('photo')->nullable();
            $table->string('cnote_number', 50)->unique();
            $table->string('pod_code', 10)->nullable();
            $table->string('city_name', 100)->nullable();
            $table->string('cust_type', 10)->nullable();
            $table->text('signature')->nullable();
            $table->dateTime('cnote_date')->nullable();
            $table->text('keterangan')->nullable();
            $table->string('pod_status', 20)->nullable();
            $table->decimal('price_per_kg', 10, 2)->nullable();
            $table->text('last_status')->nullable();
            $table->string('service_type', 10)->nullable();
            $table->decimal('cnote_amount', 10, 2)->nullable();
            $table->string('cnote_origin', 50)->nullable();
            $table->decimal('cnote_weight', 10, 2)->nullable();
            $table->decimal('shipping_cost', 10, 2)->nullable();
            $table->string('cnote_cust_no', 50)->nullable();
            $table->dateTime('cnote_pod_date')->nullable();
            $table->decimal('freight_charge', 10, 2)->nullable();
            $table->decimal('insurance_amount', 10, 2)->nullable();
            $table->string('reference_number', 50)->nullable();
            $table->string('cnote_destination', 50)->nullable();
            $table->text('cnote_goods_descr')->nullable();
            $table->string('estimate_delivery', 20)->nullable();
            $table->string('cnote_pod_receiver', 100)->nullable();
            $table->string('cnote_receiver_name', 100)->nullable();
            $table->string('cnote_services_code', 10)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cnotes');
    }
};
