<?php
/**
 * Created by XZ Software.
 * Smart code for smart wallet
 * http://xzsoftware.pl
 * User adrianmodliszewski
 * Date: 21/01/2019
 * Time: 17:51
 */

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Ramsey\Uuid\Uuid;

class UpdateTableUsersAddApiKey extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->uuid('api_key')->default(null);
        });


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('api_key');
        });
    }
}
