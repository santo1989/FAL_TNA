<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSewingBalancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sewing_balances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sewing_plan_id')->nullable();
            $table->unsignedBigInteger('job_id')->nullable()->index();
            $table->string('job_no')->nullable();
            $table->date('sewing_date')->nullable()->index();
            $table->string('color')->nullable();
            $table->string('size')->nullable();
            $table->bigInteger('sewing_balance')->nullable();
            $table->string('production_plan')->nullable()->index();
            $table->decimal('production_min_balance', 19, 4)->nullable();
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
        Schema::dropIfExists('sewing_balances');
    }
}
