<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMarchentSOPSTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('marchent_s_o_p_s', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('buyer_id')->nullable();
            $table->unsignedBigInteger('sop_id')->nullable();
            $table->string('buyer_name')->nullable();
            $table->string('Perticulars')->nullable();
            $table->string('day')->nullable();
            $table->string('status')->nullable();
            $table->string('assign_date')->nullable();
            $table->string('assign_by')->nullable();
            $table->string('remarks')->nullable();
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
        Schema::dropIfExists('marchent_s_o_p_s');
    }
}
