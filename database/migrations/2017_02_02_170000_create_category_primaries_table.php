<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCategoryPrimariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		if (env('APP_ENV') == "local" && Schema::hasTable('category_primaries_backup')) {
			Schema::dropIfExists('category_primaries');
			Schema::rename('category_primaries_backup', 'category_primaries');
		}
		elseif(!Schema::hasTable('category_primaries')) {
			Schema::create('category_primaries', function (Blueprint $table) {
				$table->increments('id');
				$table->string('name')->index();
				$table->string('slug')->unique();
				$table->integer('count')->default(0);
				$table->string('image')->nullable();
				$table->text('description')->nullable();
				$table->text('seo_meta_title')->nullable();
				$table->text('seo_meta_description')->nullable();
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
		if (env('APP_ENV') == "local" && Schema::hasTable('category_primaries')) {
			Schema::rename('category_primaries', 'category_primaries_backup');
		} 
		else {
			Schema::dropIfExists('category_primaries');
		}
    }
}
