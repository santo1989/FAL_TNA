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
            $table->unsignedBigInteger('job_id')->nullable();
            $table->string('job_no')->nullable();
            $table->string('color')->nullable();
            $table->string('size')->nullable();
            $table->decimal('shipped_qty', 8, 2)->nullable();
            $table->decimal('total_shipped_qty', 8, 2)->nullable();
            $table->decimal('total_shipped_value', 8, 2)->nullable();
            $table->date('ex_factory_date')->nullable();
            $table->decimal('shipped_value', 8, 2)->nullable();
            $table->decimal('excess_short_shipment_qty')->nullable();
            $table->decimal('excess_short_shipment_value', 8, 2)->nullable();
            $table->string('delivery_status')->nullable(); 
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
