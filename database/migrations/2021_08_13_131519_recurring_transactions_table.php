<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RecurringTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recurring', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned();

            $table->bigInteger('account_id')->unsigned();
            $table->foreign('account_id')
                ->references('id')
                ->on('account_flows')
                ->onDelete('cascade');// If a user is deleted, cascade to delete their advisor details

            $table->string('title');
            $table->text('description')->nullable();

            $table->float('value');

            $table->date('date_start');
            $table->date('date_end')->nullable();

            $table->integer('repeat_every_number')->default(1);
            $table->enum('repeat_every_type', array_keys(\App\Models\RecurringTransactions::getRepeatTimeArray()))
                ->default(\App\Models\RecurringTransactions::REPEAT_WEEK);

            $table->json('repeat_rules')->nullable();

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
        Schema::dropIfExists('recurring');
    }
}
