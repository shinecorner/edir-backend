<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
	public function up()
	{
		Schema::dropIfExists('company_images');

		Schema::create('images', function (Blueprint $table) {
			$table->increments('id');
			$table->string('image');
			$table->string('title')->nullable();
			$table->string('imageable_type');
			$table->unsignedInteger('imageable_id');
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
		Schema::dropIfExists('images');
	}
}
