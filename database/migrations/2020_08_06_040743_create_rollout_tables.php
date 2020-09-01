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
        Schema::create('rollouts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigIncrements('business_id');
            $table->foreign('business_id')->references('id')->on('businesses')->onDelete('cascade'); // If a business is deleted, cascade to delete their rollout
            $table->timestamps();
        });
        Schema::create('phases', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigIncrements('rollout_id');
            $table->foreign('rollout_id')->references('id')->on('rollouts')->onDelete('cascade'); // If an rollour is deleted, cascade to delete their phases
            $table->timestamps();
        });
        Schema::create('allocations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
        });
        Schema::create('allocation_bank_account', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigIncrements('allocation_id');
            $table->foreign('allocation_id')->references('id')->on('allocations')->onDelete('cascade'); // If an allocation is deleted, cascade to delete their pivot table rows
            $table->unsignedBigIncrements('bank_account_id');
            $table->foreign('bank_account_id')->references('id')->on('bank_accounts')->onDelete('cascade'); // If an allocation is deleted, cascade to delete their pivot table rows
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
        Schema::dropIfExists('rollouts');
        Schema::dropIfExists('phases');
        Schema::dropIfExists('allocations');
        Schema::dropIfExists('allocation_bank_account');
    }
}
