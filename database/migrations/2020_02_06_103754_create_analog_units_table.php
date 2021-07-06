<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAnalogUnitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('analog_units', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('serial_number');
            $table->string('a1')->default('℃');
            $table->string('a2')->default('℃');
            $table->string('a3')->default('%');
            $table->string('a4')->default('V');
            $table->string('a5')->default('V');
            $table->string('a6')->default('V');
            $table->string('a7')->default('A');
            $table->string('a8')->default('A');
            $table->string('a9')->default('A');
            $table->string('a10')->default('kW');
            $table->string('a11')->default('kW');
            $table->string('a12')->default('kW');
            $table->string('a13')->default('℃');
            $table->string('a14')->default('℃');
            $table->string('a15')->default('%');
            $table->string('a16')->default('V');
            $table->string('a17')->default('V');
            $table->string('a18')->default('V');
            $table->string('a19')->default('A');
            $table->string('a20')->default('A');
            $table->string('a21')->default('A');
            $table->string('a22')->default('kW');
            $table->string('a23')->default('kW');
            $table->string('a24')->default('kW');
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
        Schema::dropIfExists('analog_units');
    }
}
