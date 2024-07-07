<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTnaExplanationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tna_explanations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tna_id');
            $table->string('perticulars');
            $table->unsignedBigInteger('input_by');
            $table->date('actual_date')->nullable();
            $table->text('explanation')->nullable();
            $table->timestamps();

            $table->foreign('tna_id')->references('id')->on('t_n_a_s')->onDelete('cascade');
            $table->foreign('input_by')->references('id')->on('users')->onDelete('cascade'); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tna_explanations');
    }
}
