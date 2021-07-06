<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSiteStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('site_status', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('user_id')->nullable();
            $table->string('serial_number');
            $table->string('HV_status')->default('N');
            $table->string('DT_status')->default('N');
            $table->string('alarm_status')->default('N');
            $table->string('Up_AStatus')->default('N');
            $table->string('Up_BStatus')->default('N');
            $table->string('Up_CStatus')->default('N');
            $table->string('UP_DStatus')->default('N');
            $table->string('Up_A')->default('N');
            $table->string('Up_B')->default('N');
            $table->string('Up_C')->default('N');
            $table->string('UP_D')->default('N');
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
        Schema::dropIfExists('site_status');
    }
}