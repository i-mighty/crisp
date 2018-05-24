<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounts', function (Blueprint $table) {
        	$table->integer('user_id');
        	$table->string('token');
        	$table->string('number');
        	$table->float('balance');
            $table->increments('id');
            $table->timestamps();
        });
	    DB::update("ALTER TABLE MY_TABLE AUTO_INCREMENT = 111111;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('accounts');
    }
}
