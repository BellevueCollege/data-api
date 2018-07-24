<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('da')->create('Clients', function (Blueprint $table) {
            $table->increments('id');
            $table->string('clientname');
            $table->string('clientid')->unique();
            $table->string('clienturl');
            $table->string('clientkey');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('da')->dropIfExists('Clients');
    }
}
