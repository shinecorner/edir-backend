<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompanyCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		if (env('APP_ENV') == "local" && Schema::hasTable('company_categories_backup')) {
			Schema::dropIfExists('company_categories');
			Schema::rename('company_categories_backup', 'company_categories');
		} elseif(!Schema::hasTable('company_categories')) {
			Schema::create('company_categories', function (Blueprint $table) {
				$table->increments('id');
				$table->integer('company_id')->unsigned();
				$table->foreign('company_id')->references('id')->on('companies');
				$table->integer('category_primary_id')->unsigned();
				$table->foreign('category_primary_id')->references('id')->on('category_primaries');
				$table->integer('category_secondary_id')->unsigned();
				$table->foreign('category_secondary_id')->references('id')->on('category_secondaries');
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
		if (env('APP_ENV') == "local" && Schema::hasTable('company_categories')) {
			Schema::rename('company_categories', 'company_categories_backup');
		}
		else {
			Schema::dropIfExists('company_categories');
		}
    }
}
