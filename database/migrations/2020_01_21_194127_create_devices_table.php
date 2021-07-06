<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDevicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('devices', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('serial_number')->unique();
            $table->string('name');
            $table->string('INJstation');
            $table->string('feeder');
            $table->string('status');
            $table->string('model')->nullable();
            $table->string('network');
            $table->string('phone_number');
            $table->string('subscription_date');
            $table->string('activation');
            $table->unsignedBigInteger('device_category_id');

            $table->foreign('device_category_id')->references('id')->on('device_categories')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->unsignedBigInteger('user_id');

            $table->foreign('user_id')->references('id')->on('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->unsignedBigInteger('site_id');

            $table->foreign('site_id')->references('id')->on('sites')->nullable()
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
        Schema::dropIfExists('devices');
    }
}
