<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommunicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('communications', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('sms_user_type');
            $table->string('sms_mobile_number');
            $table->string('sms_enable');
            $table->string('email_user_type');
            $table->string('email_address');
            $table->string('email_enable');
            $table->string('schedule_time')->default("0");
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('owner_id')->nullable();
            $table->string('role')->nullable();
            $table->foreign('user_id')->references('id')->on('users')
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
        Schema::dropIfExists('communications');
    }
}
