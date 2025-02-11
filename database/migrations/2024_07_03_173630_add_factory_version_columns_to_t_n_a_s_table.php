<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFactoryVersionColumnsToTNASTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_n_a_s', function (Blueprint $table) {
            // Add columns for actual and plan versions for each process
            $table->string('print_wash')->nullable();
            $table->date('fabrics_and_accessories_inspection_plan')->nullable();
            $table->date('fabrics_and_accessories_inspection_actual')->nullable();
            $table->date('size_set_making_plan')->nullable();
            $table->date('size_set_making_actual')->nullable();
            $table->date('pattern_correction_plan')->nullable();
            $table->date('pattern_correction_actual')->nullable();
            $table->date('machines_layout_plan')->nullable();
            $table->date('machines_layout_actual')->nullable();
            $table->date('print_start_plan')->nullable();
            $table->date('print_start_actual')->nullable();
            // $table->date('emb_start_plan')->nullable();
            // $table->date('emb_start_actual')->nullable();
            $table->date('bulk_sewing_input_plan')->nullable();
            $table->date('bulk_sewing_input_actual')->nullable();
            $table->date('bulk_wash_start_plan')->nullable();
            $table->date('bulk_wash_start_actual')->nullable();
            $table->date('bulk_finishing_start_plan')->nullable();
            $table->date('bulk_finishing_start_actual')->nullable();
            $table->date('bulk_cutting_close_plan')->nullable();
            $table->date('bulk_cutting_close_actual')->nullable();
            $table->date('print_close_plan')->nullable();
            $table->date('print_close_actual')->nullable();
            // $table->date('emb_close_plan')->nullable();
            // $table->date('emb_close_actual')->nullable();
            $table->date('bulk_sewing_close_plan')->nullable();
            $table->date('bulk_sewing_close_actual')->nullable();
            $table->date('bulk_wash_close_plan')->nullable();
            $table->date('bulk_wash_close_actual')->nullable();
            // $table->date('finishing_received_plan')->nullable();
            // $table->date('finishing_received_actual')->nullable();
            $table->date('bulk_finishing_close_plan')->nullable();
            $table->date('bulk_finishing_close_actual')->nullable();
            $table->date('pre_final_inspection_plan')->nullable();
            $table->date('pre_final_inspection_actual')->nullable();
            $table->date('final_inspection_plan')->nullable();
            // $table->date('final_inspection_actual')->nullable();
            $table->date('ex_factory_plan')->nullable();
            // $table->date('ex_factory_actual')->nullable();
            $table->unsignedBigInteger('job_id')->nullable();
            $table->string('tnas1')->nullable();
            $table->string('tnas2')->nullable();
            $table->string('tnas3')->nullable();
            $table->string('tnas4')->nullable();
            $table->string('tnas5')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('t_n_a_s', function (Blueprint $table) {
            //drop
            $table->dropColumn([
                'print_wash',
                'fabrics_and_accessories_inspection_plan',
                'fabrics_and_accessories_inspection_actual',
                'size_set_making_plan',
                'size_set_making_actual',
                'pattern_correction_plan',
                'pattern_correction_actual',
                'machines_layout_plan',
                'machines_layout_actual',
                'print_start_plan',
                'print_start_actual',
                // 'emb_start_plan',
                // 'emb_start_actual',
                'bulk_sewing_input_plan',
                'bulk_sewing_input_actual',
                'bulk_wash_start_plan',
                'bulk_wash_start_actual',
                'bulk_finishing_start_plan',
                'bulk_finishing_start_actual',
                'bulk_cutting_close_plan',
                'bulk_cutting_close_actual',
                'print_close_plan',
                'print_close_actual',
                // 'emb_close_plan',
                // 'emb_close_actual',
                'bulk_sewing_close_plan',
                'bulk_sewing_close_actual',
                'bulk_wash_close_plan',
                'bulk_wash_close_actual',
                // 'finishing_received_plan',
                // 'finishing_received_actual',
                'bulk_finishing_close_plan',
                'bulk_finishing_close_actual',
                'pre_final_inspection_plan',
                'pre_final_inspection_actual',
                'final_inspection_plan',
                // 'final_inspection_actual',
                'ex_factory_plan',
                // 'ex_factory_actual',
                'job_id',
                'tnas1',
                'tnas2',
                'tnas3',
                'tnas4',
                'tnas5',
                
            ]);
        });
    }
}
