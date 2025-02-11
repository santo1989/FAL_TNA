<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInspectionAndShipmentDatesToTNASTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_n_a_s', function (Blueprint $table) {
            $table->date('inspection_actual_date')->nullable(); 
            $table->date('shipment_actual_date')->nullable();
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
            $table->dropColumn(['inspection_actual_date', 'shipment_actual_date']);
        });
    }
}
