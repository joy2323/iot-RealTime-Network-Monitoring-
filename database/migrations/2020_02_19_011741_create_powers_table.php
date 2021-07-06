<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePowersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('powers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('serial_number');
            $table->string('current_power')->default('0000.00');
            $table->string('power_hourly')->default('0000.00');
            $table->string('power_daily')->default('0000.00');
            $table->string('power_weekly')->default('0000.00');
            $table->string('power_monthly')->default('0000.00');
            $table->string('power_yearly')->default('0000.00');
            $table->string('hour')->default(date('H'));
            $table->string('day')->default(date('d'));
            $table->string('week')->default(date('w'));
            $table->string('month')->default(date('m'));
            $table->string('year')->default(date('Y'));
            $table->string('countperhr')->default('0');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->unsignedBigInteger('site_id');

            $table->foreign('site_id')->references('id')->on('sites')
                ->onDelete('cascade')
                ->onUpdate('cascade');
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
        Schema::dropIfExists('powers');
    }
}
