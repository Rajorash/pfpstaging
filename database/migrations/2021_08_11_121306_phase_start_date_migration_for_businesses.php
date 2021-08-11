<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PhaseStartDateMigrationForBusinesses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('businesses', function (Blueprint $table) {
            $table->date('start_date')->nullable();
        });
        Schema::table('businesses', function (Blueprint $table) {
            DB::statement('
                UPDATE businesses
                SET start_date = created_at
                WHERE
                    start_date IS NULL;');
        });

        Schema::table('phases', function (Blueprint $table) {
            $table->date('start_date')->nullable()->after('business_id');
        });
        Schema::table('phases', function (Blueprint $table) {
            DB::statement('
                UPDATE phases
                    SET start_date = DATE_ADD( DATE_SUB( end_date, INTERVAL 3 MONTH ), INTERVAL 1 DAY )
                WHERE
                    start_date IS NULL;');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('businesses', function (Blueprint $table) {
            $table->dropColumn('start_date');
        });
        Schema::table('phases', function (Blueprint $table) {
            $table->dropColumn('start_date');
        });
    }
}
