<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateLicenseFields2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table('licenses', function (Blueprint $table) {
            $table->unsignedBigInteger('advisor_id')->default(null)->nullable()->change();
            $table->unsignedBigInteger('business_id')->default(null)->nullable()->change();

            /**
             * Adding new columns for more comprehensive license
             * information
             */
            $table->unsignedBigInteger('regionaladmin_id')->default(null)->nullable();
            $table->foreign('regionaladmin_id')
                ->after('active')
                ->references('id')
                ->on('users')
                // If an admin is deleted, cascade to delete their licenses
                ->onDelete('cascade');
            $table->timestamp('issued_ts')
                ->useCurrent();
            $table->timestamp('assigned_ts')
                ->default(null)
                ->nullable();
            $table->timestamp('expires_ts')
                ->default(null)
                ->nullable();
            $table->timestamp('revoked_ts')
                ->default(null)
                ->nullable();

            $table->dropColumn('available_count');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('licenses', 'regionaladmin_id')) {
            Schema::table('licenses', function (Blueprint $table) {
                $table->dropForeign('licenses_regionaladmin_id_foreign');
                $table->dropColumn(['regionaladmin_id']);
            });
        }

        Schema::table('licenses', function (Blueprint $table) {
            $table->unsignedBigInteger('advisor_id')->change();
            $table->unsignedBigInteger('business_id')->change();

            $table->dropColumn([
                'issued_ts',
                'assigned_ts',
                'expires_ts',
                'revoked_ts',
            ]);
        });
    }
}
