<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCtrlFeedbackTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ctrl_feedback', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('serial_number');
            $table->string('z1')->default('0');
            $table->string('z2')->default('0');
            $table->string('z3')->default('0');
            $table->string('z4')->default('0');
            $table->string('z5')->default('0');
            $table->string('z6')->default('0');
            $table->string('z7')->default('0');
            $table->string('z8')->default('0');
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
        Schema::dropIfExists('ctrl_feedback');
    }
}
