<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateTableSpamlistsAddDeletedAt extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('spamlists', function (Blueprint $table) {
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
        Schema::table('spamlists', function (Blueprint $table) {
            //
        });
    }
}
