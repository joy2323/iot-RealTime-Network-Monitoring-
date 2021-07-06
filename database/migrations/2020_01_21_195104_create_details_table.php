<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('serial_number')->nullable();
            $table->string('a1')->default('N');
            $table->string('a2')->default('N');
            $table->string('a3')->default('N');
            $table->string('a4')->default('N');
            $table->string('a5')->default('N');
            $table->string('a6')->default('N');
            $table->string('a7')->default('N');
            $table->string('a8')->default('N');
            $table->string('a9')->default('N');
            $table->string('a10')->default('R');
            $table->string('a11')->default('R');
            $table->string('a12')->default('R');
            $table->string('a13')->default('R');
            $table->string('a14')->default('R');
            $table->string('a15')->default('R');
            $table->string('a16')->default('R');
            $table->string('a17')->default('R');
            $table->string('a18')->default('R');
            $table->string('a19')->default('R');
            $table->string('a20')->default('R');
            $table->string('a21')->default('R');
            $table->string('a22')->default('R');
            $table->string('a23')->default('R');
            $table->string('a24')->default('R');


            $table->string('d1')->default('R');
            $table->string('d2')->default('R');
            $table->string('d3')->default('R');
            $table->string('d4')->default('R');
            $table->string('d5')->default('R');
            $table->string('d6')->default('R');
            $table->string('d7')->default('R');
            $table->string('d8')->default('R');
            $table->string('d9')->default('R');
            $table->string('d10')->default('R');
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
        Schema::dropIfExists('details');
    }
}
