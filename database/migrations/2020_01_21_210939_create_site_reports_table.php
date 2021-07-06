<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSiteReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('site_reports', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('site_id');
            $table->foreign('site_id')->references('id')->on('sites')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->unsignedBigInteger('device_id');
            $table->foreign('device_id')->references('id')->on('devices')
                ->onDelete('cascade')
                ->onUpdate('cascade');
                
                $table->string('alarm');
                $table->string('duration');
                $table->string('status');
                $table->integer('stop_display');
                $table->integer('stop_message')->default('0'); //0 = message has not been sent, 1 = message has been sent;
                $table->integer('total_responder');
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
        Schema::dropIfExists('site_reports');
    }
}
