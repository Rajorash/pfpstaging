<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Pipelinemigration extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pipeline', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned();

            $table->bigInteger('business_id')->unsigned();
            $table->foreign('business_id')
                ->references('id')
                ->on('businesses')
                ->onDelete('cascade');// If a user is deleted, cascade to delete their advisor details

            $table->string('title');
            $table->text('notes')->nullable();
            $table->integer('certainly')->default(70);
            $table->text('description')->nullable();

            $table->float('value');

            $table->date('date_start');
            $table->date('date_end')->nullable();

            $table->integer('repeat_every_number')->default(1);
            $table->enum('repeat_every_type', array_keys(\App\Models\Pipeline::getRepeatTimeArray()))
                ->default(\App\Models\Pipeline::REPEAT_WEEK);

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
        Schema::dropIfExists('pipeline');
    }
}
