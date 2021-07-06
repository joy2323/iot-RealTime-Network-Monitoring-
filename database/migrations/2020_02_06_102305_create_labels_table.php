<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLabelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('labels', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('serial_number')->unique();
            $table->string('a1')->default('a1');
            $table->string('a2')->default('a2');
            $table->string('a3')->default('a3');
            $table->string('a4')->default('a4');
            $table->string('a5')->default('a5');
            $table->string('a6')->default('a6');
            $table->string('a7')->default('a7');
            $table->string('a8')->default('a8');
            $table->string('a9')->default('a9');
            $table->string('a10')->default('a10');
            $table->string('a11')->default('a11');
            $table->string('a12')->default('a12');
            $table->string('a13')->default('a13');
            $table->string('a14')->default('a14');
            $table->string('a15')->default('a15');
            $table->string('a16')->default('a16');
            $table->string('a17')->default('a17');
            $table->string('a18')->default('a18');
            $table->string('a19')->default('a19');
            $table->string('a20')->default('a20');
            $table->string('a21')->default('a21');
            $table->string('a22')->default('a22');
            $table->string('a23')->default('a23');
            $table->string('a24')->default('a24');

            $table->string('d1')->default('d1');
            $table->string('d2')->default('d2');
            $table->string('d3')->default('d3');
            $table->string('d4')->default('d4');
            $table->string('d5')->default('d5');
            $table->string('d6')->default('d6');
            $table->string('d7')->default('d7');
            $table->string('d8')->default('d8');
            $table->string('d9')->default('d9');
            $table->string('d10')->default('d10');
            $table->string('d11')->default('d11');
            $table->string('d12')->default('d12');
            $table->string('d13')->default('d13');
            $table->string('d14')->default('d14');
            $table->string('d15')->default('d15');
            $table->string('d16')->default('d16');
            $table->string('d17')->default('d17');
            $table->string('d18')->default('d18');
            $table->string('d19')->default('d19');
            $table->string('d20')->default('d20');
            $table->string('d21')->default('d21');
            $table->string('d22')->default('d22');
            $table->string('d23')->default('d23');
            $table->string('d24')->default('d24');
            $table->unsignedBigInteger('site_id');
            $table->foreign('site_id')->references('id')->on('sites')
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
        Schema::dropIfExists('labels');
    }
}
