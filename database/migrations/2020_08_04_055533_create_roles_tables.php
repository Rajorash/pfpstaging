<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRolesTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->unique();
            $table->string('label')->nullable();
            $table->timestamps();
        });

        Schema::create('role_user', function (Blueprint $table) {
            $table->primary(['user_id', 'role_id']);
            $table->bigInteger('user_id')->unsigned();
            $table->foreign('user_id')
            ->references('id')
            ->on('users')
            ->onDelete('cascade'); // If a user is deleted, cascade to delete their pivot table rows
            $table->bigInteger('role_id')->unsigned();
            $table->foreign('role_id')
            ->references('id')
            ->on('roles')
            ->onDelete('cascade'); // If a role is deleted, cascade to delete their pivot table rows
            $table->timestamps();
        });

        Schema::create('permissions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->unique();
            $table->string('label')->nullable();
            $table->timestamps();
        });

        Schema::create('permission_role', function (Blueprint $table) {
            $table->primary(['permission_id', 'role_id']);
            $table->bigInteger('permission_id')->unsigned();
            $table->foreign('permission_id')
            ->references('id')
            ->on('permissions')
            ->onDelete('cascade'); // If a permission is deleted, cascade to delete their pivot table rows
            $table->bigInteger('role_id')->unsigned();
            $table->foreign('role_id')
            ->references('id')
            ->on('roles')
            ->onDelete('cascade'); // If a role is deleted, cascade to delete their pivot table rows
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
        Schema::dropIfExists('permission_role');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('role_user');
        Schema::dropIfExists('roles');
    }
}
