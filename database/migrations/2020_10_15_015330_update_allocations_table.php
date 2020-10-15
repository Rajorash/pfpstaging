<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateAllocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('allocations', function (Blueprint $table) {
            $table->unsignedBigInteger('phase_id')->after('id');
            $table->foreign('phase_id')->references('id')->on('phases')->onDelete('cascade'); // If an allocation is deleted, cascade to delete their pivot table rows
            $table->date('allocation_date')->after('phase_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('allocations', function (Blueprint $table) {
            //
        });
    }
}
