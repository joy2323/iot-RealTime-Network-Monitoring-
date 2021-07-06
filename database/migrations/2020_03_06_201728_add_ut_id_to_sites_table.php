<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUtIdToSitesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sites', function (Blueprint $table) {
            $table->string('ut_id')->nullable()->after('created_at');
            $table->string('client_id')->nullable()->after('created_at');
            $table->string('siteuser_id')->nullable()->after('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sites', function (Blueprint $table) {
            $table->dropColumn('ut_id');
            $table->dropColumn('client_id');
            $table->dropColumn('siteuser_id');

        });
    }
}
