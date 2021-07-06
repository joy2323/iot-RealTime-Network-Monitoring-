<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUprisersColumnsToSiteStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('site_status', function (Blueprint $table) {
            $table->string('Up_A')->nullable()->after('user_id');
            $table->string('Up_B')->nullable()->after('Up_A');
            $table->string('Up_C')->nullable()->after('Up_B');
            $table->string('Up_D')->nullable()->after('Up_C');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('site_status', function (Blueprint $table) {
            $table->dropColumn('Up_A');
            $table->dropColumn('Up_B');
            $table->dropColumn('Up_C');
            $table->dropColumn('Up_D');
        });
    }
}
