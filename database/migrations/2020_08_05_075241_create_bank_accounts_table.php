<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBankAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bank_accounts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->enum('type', ['revenue', 'pretotal', 'salestax', 'prereal', 'postreal']);
            $table->unsignedBigInteger('business_id');
            $table->foreign('business_id')
            ->references('id')
            ->on('businesses')
            ->onDelete('cascade'); // If a business is deleted, cascade to delete their accounts
            $table->timestamps();
        });

        Schema::create('account_flows', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('label');
            $table->boolean('negative_flow')->default(false);
            $table->unsignedBigInteger('account_id');
            $table->foreign('account_id')
            ->references('id')
            ->on('bank_accounts')
            ->onDelete('cascade'); // If an account is deleted, cascade to delete their flows
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
        Schema::dropIfExists('bank_accounts');
        Schema::dropIfExists('account_flows');
    }
}
