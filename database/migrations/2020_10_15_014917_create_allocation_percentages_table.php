<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAllocationPercentagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('allocation_percentages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('phase_id');
            $table->foreign('phase_id')->references('id')->on('phases')->onDelete('cascade'); // If an allocation is deleted, cascade to delete their pivot table rows
            $table->unsignedBigInteger('bank_account_id');
            $table->foreign('bank_account_id')->references('id')->on('bank_accounts')->onDelete('cascade'); // If an allocation is deleted, cascade to delete their pivot table rows
            $table->decimal('percent', 5,2);
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
        Schema::dropIfExists('allocation_percentages');
    }
}
