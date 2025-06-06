<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShipmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('job_id')->nullable()->index();
            $table->string('job_no')->nullable();
            $table->string('color')->nullable();
            $table->string('size')->nullable();
            $table->bigInteger('shipped_qty')->nullable();
            $table->bigInteger('total_shipped_qty')->nullable();
            $table->decimal('total_shipped_value', 19, 4)->nullable();
            $table->date('ex_factory_date')->nullable()->index();
            $table->decimal('shipped_value', 19, 4)->nullable();
            $table->bigInteger('excess_short_shipment_qty')->nullable();
            $table->decimal('excess_short_shipment_value', 19, 4)->nullable();
            $table->string('delivery_status')->nullable();
            $table->string('shipment_status')->nullable()->index();
            $table->string('shipment_remarks')->nullable();
            $table->string('shipment1')->nullable();
            $table->string('shipment2')->nullable();
            $table->string('shipment3')->nullable();
            $table->string('shipment4')->nullable();
            $table->string('shipment5')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shipments');
    }
}
