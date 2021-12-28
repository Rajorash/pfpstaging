<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPercenatageForFlow extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('account_flows', function (Blueprint $table) {
            $table->integer('certainly')->default(100);
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
            $table->dropColumn('certainly');
        });
    }
}
