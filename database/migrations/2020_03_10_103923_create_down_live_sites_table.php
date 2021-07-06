<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDownLiveSitesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('down_live_sites', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('serial_number');
            $table->string('site_id')->nullable();
            $table->string('up_time')->default("00:00:00");
            $table->string('down_time')->default("00:00:00");
            $table->string('up_duration')->default("0");
            $table->string('down_duration')->default("0");
            $table->string('power')->nullable();
            $table->string('energy')->nullable();
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
        Schema::dropIfExists('down_live_sites');
    }
}
