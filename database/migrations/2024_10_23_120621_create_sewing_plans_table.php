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
            $table->unsignedBigInteger('job_id')->nullable();
            $table->string('job_no')->nullable();
            $table->string('production_plan')->nullable(); 
            $table->string('color')->nullable();
            $table->string('size')->nullable();
            $table->decimal('color_quantity', 8, 2)->nullable(); 
            $table->timestamps();
        });
    }

    
    public function down()
    {
        Schema::dropIfExists('sewing_plans');
    }
}
