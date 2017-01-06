<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableQueue extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('queue', function (Blueprint $table) {
            $table->increments('id');

            $table->text('content')->nullable();
            $table->text('users')->nullable();
            $table->string('embed')->nullable();
            $table->text('result')->nullable();
            $table->integer('user_id')->unsigned();
            $table->integer('entry_id', false, true)->nullable();
            $table->string('user_key')->nullable();
            $table->integer('user_call_limit')->unsigned();
            $table->timestamp('post_after');
            $table->smallInteger('fallback')->default(0);

            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('queue');
    }
}
