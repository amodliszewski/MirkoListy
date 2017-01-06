<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateTableSpamlistsAddCityCategory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('spamlists', function (Blueprint $table) {
            $table->integer('city_id')->unsigned()->nullable();
            $table->integer('category_id')->unsigned()->nullable();

            $table->foreign('city_id')->references('id')->on('cities');
            $table->foreign('category_id')->references('id')->on('categories');
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
