<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFactoryHolidaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('factory_holidays', function (Blueprint $table) {
            $table->id();
            $table->date('holiday_date');
            $table->boolean('is_default')->default(false); // For Friday or auto-added holidays
            $table->boolean('is_weekend')->default(false); // For Saturday or Sunday
            $table->boolean('is_additional')->default(false); // For Additional holidays
            $table->boolean('is_active')->default(true);
            $table->string('description')->nullable(); // Optional description
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
        Schema::dropIfExists('factory_holidays');
    }
}
