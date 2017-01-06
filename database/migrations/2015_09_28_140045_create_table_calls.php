<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableCalls extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('calls', function (Blueprint $table) {
            $table->increments('id');

            $table->string('author');
            $table->string('author_avatar');
            $table->string('author_sex')->nullable();
            $table->string('author_group');
            $table->timestamp('posted_at');
            $table->text('content');
            $table->integer('entry_id', false, true);
            $table->string('image_url')->nullable();
            $table->integer('user_id')->unsigned();

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
        Schema::drop('calls');
    }
}
