<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSewingPlansTable extends Migration
{
    
    public function up()
    {
        Schema::create('sewing_plans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('job_id')->nullable()->index();
            $table->string('job_no')->nullable();
            $table->string('production_plan')->nullable()->index(); 
            $table->string('color')->nullable();
            $table->string('size')->nullable();
            $table->bigInteger('color_quantity')->nullable(); 
            $table->string('sewing_plans1')->nullable();
            $table->string('sewing_plans2')->nullable();
            $table->string('sewing_plans3')->nullable();
            $table->string('sewing_plans4')->nullable();
            $table->string('sewing_plans5')->nullable();
            $table->timestamps();
        });
    }

    
    public function down()
    {
        Schema::dropIfExists('sewing_plans');
    }
}
