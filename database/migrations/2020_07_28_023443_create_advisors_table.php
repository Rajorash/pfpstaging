<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdvisorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('advisors', function (Blueprint $table) {
            $table->bigInteger('id')->unsigned();
            $table->smallInteger('seat_limit');
            $table->string('niche');
            $table->string('tier');
            $table->foreign('id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade'); // If a user is deleted, cascade to delete their advisor details
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('advisors');
    }
}
