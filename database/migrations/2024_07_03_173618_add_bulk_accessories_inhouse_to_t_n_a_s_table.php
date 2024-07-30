<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBulkAccessoriesInhouseToTNASTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_n_a_s', function (Blueprint $table) {
           $table->date('bulk_accessories_inhouse_plan')->nullable();
           $table->date('bulk_accessories_inhouse_actual')->nullable();

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
            $table->dropColumn('bulk_accessories_inhouse_plan');
            $table->dropColumn('bulk_accessories_inhouse_actual');
        });
    }
}
