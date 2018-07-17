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
        	$table->increments('id');
        	$table->morphs('owner');
        	$table->string('token')->nullable();
        	$table->float('balance')->default(0.0);
            $table->timestamps();
        });
	    DB::update("ALTER TABLE accounts AUTO_INCREMENT = 111111;");
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
