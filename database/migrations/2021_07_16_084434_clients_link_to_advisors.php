<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ClientsLinkToAdvisors extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('client_advisors', function (Blueprint $table) {
            $table->bigInteger('client_id')->unsigned();
            $table->foreign('client_id')->references('id')->on('users');
            $table->bigInteger('advisor_id')->unsigned();
            $table->foreign('advisor_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('client_advisors');
    }
}
