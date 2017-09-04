<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDealsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('deals', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('slug')->unique();
			$table->text('summary', 250)->nullable();
			$table->text('description');
			$table->text('conditions')->nullable();

			$table->dateTime('date_start');
			$table->dateTime('date_end');

            $table->decimal('regular_price', 10, 2)->nullable();
            $table->enum('discount_type', ['none', 'fixed', 'percent'])->default('none');
            $table->decimal('discount_value', 10, 2)->nullable();
            $table->string('discount_coupon')->nullable();

            $table->string('image')->nullable();
			$table->string('video_url')->nullable();
			$table->string('product_url')->nullable();

			$table->boolean('active')->default(true)->index();
			$table->boolean('approved')->default(false)->index();

            $table->text('seo_meta_title')->nullable();
            $table->text('seo_meta_description')->nullable();
			$table->unsignedInteger('category_deal_id');
			$table->foreign('category_deal_id')->references('id')->on('category_events')->onDelete('cascade');
			$table->unsignedInteger('company_id');
			$table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->unsignedInteger('location_id')->nullable();
            $table->foreign('location_id')->references('id')->on('locations')->onDelete('set null');
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
        Schema::dropIfExists('deals');
    }
}
