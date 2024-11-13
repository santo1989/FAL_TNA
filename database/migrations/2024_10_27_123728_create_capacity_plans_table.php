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
            $table->unsignedBigInteger('division_id')->nullable();
            $table->string('division_name')->nullable();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->string('company_name')->nullable(); 
            $table->string('production_plan')->nullable();
            $table->string('running_machines')->nullable();
            $table->string('helpers')->nullable();
            $table->string('working_hours')->nullable();
            $table->string('efficiency')->nullable();
            $table->string('smv')->nullable();
            $table->string('workingDays')->nullable();
            $table->string('daily_capacity_minutes')->nullable();
            $table->string('weekly_capacity_minutes')->nullable();
            $table->string('monthly_capacity_minutes')->nullable();
            $table->string('monthly_capacity_quantity')->nullable();
            $table->string('monthly_capacity_value')->nullable(); 
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
