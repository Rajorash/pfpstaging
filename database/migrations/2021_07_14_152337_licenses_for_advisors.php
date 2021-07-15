<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class LicensesForAdvisors extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('licenses_for_advisors', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('advisor_id')->unsigned();
            $table->foreign('advisor_id')->references('id')->on('users');
            $table->bigInteger('regional_admin_id')->unsigned();
            $table->foreign('regional_admin_id')->references('id')->on('users');
            $table->integer('licenses');
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
        Schema::dropIfExists('licenses_for_advisors');
    }
}
