<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ScadaAuth extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('scada_auth', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('client_id');
            $table->string('email');
            $table->string('mac_address');
            $table->string('ip_address');
            $table->string('access_token')->nullable();
            $table->integer('activate')->default(1);
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
        //
        Schema::dropIfExists('scada_auth');
    }
}