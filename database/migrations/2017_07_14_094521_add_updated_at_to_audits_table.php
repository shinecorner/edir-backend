<?php
/**
 * This file is part of the Laravel Auditing package.
 *
 * @author     Antério Vieira <anteriovieira@gmail.com>
 * @author     Quetzy Garcia  <quetzyg@altek.org>
 * @author     Raphael França <raphaelfrancabsb@gmail.com>
 * @copyright  2015-2017
 *
 * For the full copyright and license information,
 * please view the LICENSE.md file that was distributed
 * with this source code.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddUpdatedAtToAuditsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
//		Schema::table('audits', function (Blueprint $table) {
//			$table->timestamp('updated_at')->default(\Carbon\Carbon::now()); //used by default by laravel
//		});

        Schema::connection(Config::get('audit.drivers.database.connection'))
            ->table(Config::get('audit.drivers.database.table'), function (Blueprint $table) {
                $table->timestamp('updated_at')->nullable();
            });

        DB::table('audits')->whereNull('updated_at')->update([
            'created_at' => DB::raw('created_at'),
            'updated_at' => DB::raw('created_at'),
        ]);
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::connection(Config::get('audit.drivers.database.connection'))
            ->table(Config::get('audit.drivers.database.table'), function (Blueprint $table) {
                $table->dropColumn('updated_at');
            });
	}
}
