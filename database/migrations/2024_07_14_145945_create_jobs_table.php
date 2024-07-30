<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->unsignedBigInteger('division_id')->nullable();
            $table->unsignedBigInteger('buyer_id')->nullable();
            $table->string('company_name')->nullable();
            $table->string('division_name')->nullable();
            $table->string('job_no')->nullable();
            $table->string('buyer')->nullable();
            $table->string('style')->nullable();
            $table->string('po')->nullable();
            $table->string('department')->nullable();
            $table->string('item')->nullable();
            $table->string('destination')->nullable();
            $table->string('color')->nullable();
            $table->string('size')->nullable();
            $table->decimal('color_quantity', 8, 2)->nullable();
            $table->decimal('order_quantity', 8, 2)->nullable();
            // $table->decimal('sewing_balance', 8, 2)->nullable();
            $table->string('production_plan')->nullable();
            $table->date('ins_date')->nullable();
            $table->date('delivery_date')->nullable();
            $table->decimal('target_smv', 8, 2)->nullable();
            $table->decimal('production_minutes', 8, 2)->nullable();
            // $table->decimal('production_min_balance', 8, 2)->nullable();
            $table->decimal('unit_price', 8, 2)->nullable();
            $table->decimal('total_value', 8, 2)->nullable();
            $table->decimal('cm_pc', 8, 2)->nullable();
            $table->decimal('total_cm', 8, 2)->nullable();
            $table->decimal('consumption_dzn', 8, 2)->nullable();
            $table->decimal('fabric_qnty', 8, 2)->nullable();
            $table->string('fabrication')->nullable();
            $table->date('order_received_date')->nullable();
            $table->string('aop')->nullable();
            $table->string('print')->nullable();
            $table->string('embroidery')->nullable();
            $table->text('remarks')->nullable()->nullable();
            // $table->integer('shipped_qty')->nullable();
            // $table->date('ex_factory_date')->nullable();
            // $table->decimal('shipped_value', 8, 2)->nullable();
            // $table->integer('excess_short_shipment_qty')->nullable();
            // $table->decimal('excess_short_shipment_value', 8, 2)->nullable();
            // $table->string('delivery_status')->nullable(); 
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
        Schema::dropIfExists('jobs');
    }
}
