<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FixCertaintySpellingFlows extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('account_flows', function (Blueprint $table) {
            $table->renameColumn('certainly', 'certainty');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('account_flows', function (Blueprint $table) {
            $table->renameColumn('certainty', 'certainly');
        });
    }
}
