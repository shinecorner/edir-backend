<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
//		if (env('APP_ENV') == "local" && Schema::hasTable('companies_backup')) {
//			Schema::dropIfExists('companies');
//			Schema::rename('companies_backup', 'companies');
//		}
//		elseif(!Schema::hasTable('companies')) {
			Schema::create('companies', function (Blueprint $table) {
				$table->increments('id');
				$table->string('name');
				$table->string('slug')->unique();
				$table->string('email')->nullable();
				$table->string('phone', 50)->nullable();
				$table->string('mobile', 50)->nullable();
				$table->string('fax', 50)->nullable();
				$table->string('www')->nullable();

				$table->string('listing_level', 50)->default('basic'); //enum?
				$table->boolean('listing_status'); // active true (1), deactivated false (0)
				$table->date('listing_valid_until')->nullable();

    			$table->text('summary', 250)->nullable();
				$table->text('description')->nullable();

				$table->string('image')->nullable();
				$table->string('video_url')->nullable();

				$table->string('seo_meta_title')->nullable();
				$table->text('seo_meta_description')->nullable();

				$table->unsignedInteger('user_id')->nullable();
				$table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
				$table->unsignedInteger('location_id')->nullable();
				$table->foreign('location_id')->references('id')->on('locations')->onDelete('set null');

				$table->timestamps();
			});
//		}
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
	public function down()
	{
//		if (env('APP_ENV') == "local" && Schema::hasTable('companies')) {
//			Schema::rename('companies', 'companies_backup');
//		}
//		else {
			DB::statement('SET FOREIGN_KEY_CHECKS=0;');
			Schema::dropIfExists('companies');
			DB::statement('SET FOREIGN_KEY_CHECKS=1;');
//		}
    }
}
