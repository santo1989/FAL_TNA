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
            $table->unsignedBigInteger('company_id')->nullable()->index();
            $table->unsignedBigInteger('division_id')->nullable()->index();
            $table->unsignedBigInteger('buyer_id')->nullable()->index();
            $table->string('company_name')->nullable();
            $table->string('division_name')->nullable();
            $table->string('batch_id')->nullable()->index();
            $table->string('job_no')->nullable()->index();
            $table->string('buyer')->nullable();
            $table->text('style')->nullable();
            $table->text('po')->nullable();
            $table->text('department')->nullable();
            $table->text('item')->nullable();
            $table->text('destination')->nullable();
            $table->text('color')->nullable();
            $table->text('size')->nullable();
            $table->bigInteger('color_quantity')->nullable();
            $table->bigInteger('order_quantity')->nullable();
            // $table->decimal('sewing_balance', 19, 4)->nullable();
            $table->string('production_plan')->nullable()->index();
            $table->date('ins_date')->nullable();
            $table->date('delivery_date')->nullable()->index();
            $table->decimal('target_smv', 19, 4)->nullable();
            $table->decimal('production_minutes', 19, 4)->nullable();
            // $table->decimal('production_min_balance', 19, 4)->nullable();
            $table->decimal('unit_price', 19, 4)->nullable();
            $table->decimal('total_value', 19, 4)->nullable();
            $table->decimal('cm_pc', 19, 4)->nullable();
            $table->decimal('total_cm', 19, 4)->nullable();
            $table->decimal('consumption_dzn', 19, 4)->nullable();
            $table->decimal('fabric_qnty', 19, 4)->nullable();
            $table->text('fabrication')->nullable();
            $table->date('order_received_date')->nullable();
            $table->string('aop')->nullable();
            $table->string('print')->nullable();
            $table->string('embroidery')->nullable();
            $table->string('wash')->nullable();
            $table->string('print_wash')->nullable();
            $table->text('remarks')->nullable();
            $table->string('buyer_hold_shipment')->nullable();
            $table->string('buyer_hold_shipment_reason')->nullable();
            $table->string('buyer_hold_shipment_date')->nullable();
            $table->string('buyer_cancel_shipment')->nullable();
            $table->string('buyer_cancel_shipment_reason')->nullable();
            $table->string('buyer_cancel_shipment_date')->nullable();
            $table->string(
            'order_close')->nullable();
            $table->string('order_close_reason')->nullable();
            $table->string('order_close_date')->nullable();
            $table->string('order_close_by')->nullable();
            $table->string('job_column1')->nullable();
            $table->string('job_column2')->nullable();
            $table->string('job_column3')->nullable();
            $table->string('job_column4')->nullable();
            $table->string('job_column5')->nullable();
            $table->string('job_column6')->nullable();
            $table->string('job_column7')->nullable();
            $table->string('job_column8')->nullable();
            $table->string('job_column9')->nullable();
            $table->string('job_column10')->nullable();
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
