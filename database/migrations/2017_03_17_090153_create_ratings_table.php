<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRatingsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('ratings', function (Blueprint $table) {
			$table->increments('id');
            $table->string('name')->nullable();
			$table->string('title')->nullable();
			$table->text('description')->nullable();
			$table->unsignedSmallInteger('rating');
			$table->boolean('approved')->default(false)->index();
            $table->boolean('is_visible')->default(false)->index();
            $table->string('ip_address')->nullable();
			$table->unsignedInteger('company_id')->nullable();
			$table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
			$table->unsignedInteger('directory_id')->nullable();
			$table->foreign('directory_id')->references('id')->on('directories')->onDelete('cascade');
			$table->timestamps();
			$table->softDeletes();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('ratings');
	}
}