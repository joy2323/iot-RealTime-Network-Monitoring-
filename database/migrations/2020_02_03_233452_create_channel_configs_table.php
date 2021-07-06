<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChannelConfigsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('channel_configs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('serial_number')->nullable();
            $table->string('a1')->default('1');
            $table->string('a2')->default('0');
            $table->string('a3')->default('0');
            $table->string('a4')->default('1');
            $table->string('a5')->default('1');
            $table->string('a6')->default('1');
            $table->string('a7')->default('1');
            $table->string('a8')->default('1');
            $table->string('a9')->default('1');
            $table->string('a10')->default('0');
            $table->string('a11')->default('0');
            $table->string('a12')->default('0');
            $table->string('a13')->default('0');
            $table->string('a14')->default('0');
            $table->string('a15')->default('0');
            $table->string('a16')->default('0');
            $table->string('a17')->default('0');
            $table->string('a18')->default('0');
            $table->string('a19')->default('0');
            $table->string('a20')->default('0');
            $table->string('a21')->default('0');
            $table->string('a22')->default('0');
            $table->string('a23')->default('0');
            $table->string('a24')->default('0');


            $table->string('d1')->default('1');
            $table->string('d2')->default('1');
            $table->string('d3')->default('1');
            $table->string('d4')->default('1');
            $table->string('d5')->default('1');
            $table->string('d6')->default('1');
            $table->string('d7')->default('1');
            $table->string('d8')->default('1');
            $table->string('d9')->default('1');
            $table->string('d10')->default('1');
            $table->string('d11')->default('R');
            $table->string('d12')->default('R');
            $table->string('d13')->default('R');
            $table->string('d14')->default('R');
            $table->string('d15')->default('R');
            $table->string('d16')->default('R');
            $table->string('d17')->default('R');
            $table->string('d18')->default('R');
            $table->string('d19')->default('R');
            $table->string('d20')->default('R');
            $table->string('d21')->default('R');
            $table->string('d22')->default('R');
            $table->string('d23')->default('R');
            $table->string('d24')->default('R');

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
        Schema::dropIfExists('channel_configs');
    }
}
