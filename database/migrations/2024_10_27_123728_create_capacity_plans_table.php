<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCapacityPlansTable extends Migration
{
  
    public function up()
    {
        Schema::create('capacity_plans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('division_id')->nullable()->index();
            $table->string('division_name')->nullable();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->string('company_name')->nullable(); 
            $table->string('production_plan')->nullable()->index();
            $table->bigInteger('running_machines')->nullable();
            $table->bigInteger('helpers')->nullable();
            $table->bigInteger('working_hours')->nullable();
            $table->string('efficiency')->nullable();
            $table->float('smv')->nullable();
            $table->bigInteger('workingDays')->nullable();
            $table->float('daily_capacity_minutes')->nullable();
            $table->float('weekly_capacity_minutes')->nullable();
            $table->float('monthly_capacity_minutes')->nullable();
            $table->bigInteger('monthly_capacity_quantity')->nullable();
            $table->bigInteger('monthly_capacity_value')->nullable(); 
            $table->string('capacity_plans1')->nullable();
            $table->string('capacity_plans2')->nullable();
            $table->string('capacity_plans3')->nullable();
            $table->string('capacity_plans4')->nullable();
            $table->string('capacity_plans5')->nullable();
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
        Schema::dropIfExists('capacity_plans');
    }
}
