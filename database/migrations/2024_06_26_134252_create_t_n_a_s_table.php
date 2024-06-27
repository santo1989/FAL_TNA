<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTNASTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_n_a_s', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('buyer_id')->nullable();
            $table->string('buyer')->nullable();
            $table->string('style')->nullable();
            $table->string('po')->nullable();
            $table->string('picture')->nullable();
            $table->string('item')->nullable();
            $table->string('color')->nullable();
            $table->integer('qty_pcs')->nullable();
            $table->date('po_receive_date')->nullable();
            $table->date('shipment_etd')->nullable();
            $table->integer('total_lead_time')->nullable();
            $table->date('order_free_time')->nullable();
            $table->date('lab_dip_submission_plan')->nullable();
            $table->date('lab_dip_submission_actual')->nullable();
            $table->date('fabric_booking_plan')->nullable();
            $table->date('fabric_booking_actual')->nullable();
            $table->date('fit_sample_submission_plan')->nullable();
            $table->date('fit_sample_submission_actual')->nullable();
            $table->date('print_strike_off_submission_plan')->nullable();
            $table->date('print_strike_off_submission_actual')->nullable();
            $table->date('bulk_accessories_booking_plan')->nullable();
            $table->date('bulk_accessories_booking_actual')->nullable();
            $table->date('fit_comments_plan')->nullable();
            $table->date('fit_comments_actual')->nullable();
            $table->date('bulk_yarn_inhouse_plan')->nullable();
            $table->date('bulk_yarn_inhouse_actual')->nullable();
            $table->date('pp_sample_submission_plan')->nullable();
            $table->date('pp_sample_submission_actual')->nullable();
            $table->date('bulk_fabric_knitting_plan')->nullable();
            $table->date('bulk_fabric_knitting_actual')->nullable();
            $table->date('pp_comments_receive_plan')->nullable();
            $table->date('pp_comments_receive_actual')->nullable();
            $table->date('bulk_fabric_dyeing_plan')->nullable();
            $table->date('bulk_fabric_dyeing_actual')->nullable();
            $table->date('bulk_fabric_delivery_plan')->nullable();
            $table->date('bulk_fabric_delivery_actual')->nullable();
            $table->date('pp_meeting_plan')->nullable();
            $table->date('pp_meeting_actual')->nullable();
            $table->date('etd_plan')->nullable();
            $table->date('etd_actual')->nullable();
            $table->string('assign_date')->nullable();
            $table->string('assign_by')->nullable();
            $table->string('remarks')->nullable();
            $table->string('order_close')->nullable()->default('0');
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
        Schema::dropIfExists('t_n_a_s');
    }
}
