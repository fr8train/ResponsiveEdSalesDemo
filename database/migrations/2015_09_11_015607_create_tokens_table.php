<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTokensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tokens', function (Blueprint $table) {
            $table->integer('brainhoney_user_id')->unsigned()->primary();
            $table->string('token')->index();
            $table->integer('lifespan')->nullable();
            $table->timestamps();
            $table->foreign('brainhoney_user_id')->references('brainhoney_user_id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('tokens');
    }
}
