<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCategorySecondariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		if (env('APP_ENV') == "local" && Schema::hasTable('category_secondaries_backup')) {
			Schema::dropIfExists('category_secondaries');
			Schema::rename('category_secondaries_backup', 'category_secondaries');
		}
		elseif(!Schema::hasTable('category_secondaries')) {
			Schema::create('category_secondaries', function (Blueprint $table) {
				$table->increments('id');
				$table->string('name')->index();
				$table->string('slug')->index();
				$table->integer('count')->default(0);
				$table->string('image')->nullable();
				$table->text('description')->nullable();
				$table->text('seo_meta_title')->nullable();
				$table->text('seo_meta_description')->nullable();
				$table->integer('category_primary_id')->unsigned()->nullable();
				$table->foreign('category_primary_id')->references('id')->on('category_secondaries');
			});
		}
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
		if (env('APP_ENV') == "local" && Schema::hasTable('category_secondaries')) {
			Schema::rename('category_secondaries', 'category_secondaries_backup');
		}
		else {
			Schema::dropIfExists('category_secondaries');
		}
	}
}
