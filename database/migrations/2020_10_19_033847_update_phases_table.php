<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdatePhasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('phases', function (Blueprint $table) {
            // drop rollout id and constraints
            // $table->dropForeign(['rollout_id']);
            $table->dropIfExists('rollout_id');

            // add business id foreign key
            $table->unsignedBigInteger('business_id')->after('id');
            $table->foreign('business_id')
            ->references('id')
            ->on('businesses')
            ->onDelete('cascade'); // If a business is deleted, cascade to delete their phases
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
