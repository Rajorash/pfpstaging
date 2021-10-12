<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRolloutTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('phases', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('business_id');
            $table->foreign('business_id')
            ->references('id')
            ->on('businesses')
            ->onDelete('cascade'); // If a business is deleted, cascade to delete their phases
            $table->date('end_date')->nullable();
            $table->timestamps();
        });

        Schema::create('allocations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('phase_id');
            $table->foreign('phase_id')->references('id')->on('phases')->onDelete('cascade'); // If an allocation is deleted, cascade to delete their pivot table rows
            $table->bigInteger('allocatable_id')->unsigned();
            $table->string('allocatable_type');
            $table->decimal('amount', 14, 2);
            $table->date('allocation_date');
            $table->timestamps();
        });

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
        Schema::dropIfExists('allocations');
        Schema::dropIfExists('phases');
    }
}
