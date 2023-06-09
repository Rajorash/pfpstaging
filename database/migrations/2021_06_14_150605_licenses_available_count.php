<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class LicensesAvailableCount extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('licenses', function (Blueprint $table) {
            $table->integer('available_count')->default(5);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('licenses', 'available_count')) {
            Schema::table('licenses', function (Blueprint $table) {
                $table->dropColumn('available_count');
            });
        }
    }
}
