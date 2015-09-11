<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->integer('brainhoney_user_id')->unsigned()->primary();
            $table->string('username',75)->unique();
            $table->string('firstname',50);
            $table->string('lastname',50);
            $table->string('email',125)->index();
            $table->integer('domain_id')->unsigned()->index();
            $table->string('domain_name',75)->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('users');
    }
}
