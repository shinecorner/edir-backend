<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('street_name');
            $table->string('street_number', 20);
            $table->string('street_additional')->nullable();
            $table->string('zip_code', 6)->index();
            $table->string('city')->index();
            $table->string('district')->nullable();
            $table->string('county')->nullable();
            $table->string('state')->nullable()->index();
            $table->string('latitude');
            $table->string('longitude');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('locations');
    }
}
