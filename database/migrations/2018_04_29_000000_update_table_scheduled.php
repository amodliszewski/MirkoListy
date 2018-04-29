<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateTableScheduled extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('scheduled_posts', function (Blueprint $table) {
            $table->string('spamlists');
            $table->smallInteger('spamlist_sex')->default(0);
            $table->softDeletes();
            $table->integer('deleted_by')->unsigned()->nullable();

            $table->foreign('deleted_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('scheduled_posts', function (Blueprint $table) {
            //
        });
    }
}
