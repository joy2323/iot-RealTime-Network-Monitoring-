<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSitesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sites', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('site_number');
            $table->string('name');
            $table->string('trans_id')->default(null);
            $table->string('trans_code')->default(null);
            $table->string('serial_number')->unique();
            $table->string('phone_number');
            $table->string('uprisers');
            $table->string('email');
            $table->string('username');
            $table->string('password');
            $table->string('confirm_password');
            $table->string('activation');
            $table->string('ctrl_enable')->default('0');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            // $table->unsignedBigInteger('device_id');
            // $table->foreign('device_id')->references('id')->on('devices')
            //     ->onDelete('cascade')
            //     ->onUpdate('cascade');

            // $table->string('feeder');
            // $table->string('Injstation');

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
        Schema::dropIfExists('sites');
    }
}