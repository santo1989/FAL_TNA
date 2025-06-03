<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateCapacityPlansDataTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('capacity_plans', function (Blueprint $table) {
            $table->float('monthly_capacity_value')->nullable()->change();
            $table->float('efficiency')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('capacity_plans', function (Blueprint $table) {
            $table->bigInteger('monthly_capacity_value')->nullable()->change();
            $table->string('efficiency')->nullable()->change();
        });
    }
}
