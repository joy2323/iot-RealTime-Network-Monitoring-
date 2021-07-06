<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAnalogValuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('analog_values', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('serial_number');
            $table->string('a1')->default('1.0000');
            $table->string('a2')->default('1.0000');
            $table->string('a3')->default('1.0000');
            $table->string('a4')->default('1.0000');
            $table->string('a5')->default('1.0000');
            $table->string('a6')->default('1.0000');
            $table->string('a7')->default('1.0000');
            $table->string('a8')->default('1.0000');
            $table->string('a9')->default('1.0000');
            $table->string('a10')->default('1.0000');
            $table->string('a11')->default('1.0000');
            $table->string('a12')->default('1.0000');
            $table->string('a13')->default('1.0000');
            $table->string('a14')->default('1.0000');
            $table->string('a15')->default('1.0000');
            $table->string('a16')->default('1.0000');
            $table->string('a17')->default('1.0000');
            $table->string('a18')->default('1.0000');
            $table->string('a19')->default('1.0000');
            $table->string('a20')->default('1.0000');
            $table->string('a21')->default('1.0000');
            $table->string('a22')->default('1.0000');
            $table->string('a23')->default('1.0000');
            $table->string('a24')->default('1.0000');


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
        Schema::dropIfExists('analog_values');
    }
}
