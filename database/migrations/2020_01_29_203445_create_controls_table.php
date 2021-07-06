<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateControlsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('controls', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('site_id');
            $table->foreign('site_id')->references('id')->on('sites')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->string('c1')->default('0');
            $table->string('c2')->default('0');
            $table->string('c3')->default('0');
            $table->string('c4')->default('0');
            $table->string('c5')->default('0');
            $table->string('c6')->default('0');
            $table->string('c7')->default('0');
            $table->string('c8')->default('0');

            $table->string('e1')->default('00.00');
            $table->string('e2')->default('00.00');
            $table->string('e3')->default('00.00');
            $table->string('e4')->default('00.00');
            $table->string('e5')->default('00.00');
            $table->string('e6')->default('00.00');
            $table->string('e7')->default('00.00');
            $table->string('e8')->default('00.00');
            
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
        Schema::dropIfExists('controls');
    }
}
