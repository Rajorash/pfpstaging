<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBankAccountEntriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bank_account_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bank_account_id')
                  ->references('id')
                  ->on('bank_accounts')
                  ->onDelete('cascade');
            $table->date('balance_date');
            $table->decimal('balance_amount', 14, 2);
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
        Schema::dropIfExists('bank_account_entries');
    }
}
