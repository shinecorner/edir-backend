<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Postal Codes Import
		DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        // DB::table('locations_temp')->truncate();

        // Note: these dump files must be generated with DELETE (or TRUNCATE) + INSERT statements
        $sql = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'dumps' . DIRECTORY_SEPARATOR . 'locations.sql');

        // split the statements, so DB::statement can execute them.
        $statements = array_filter(array_map('trim', explode(';', $sql)));

        foreach ($statements as $stmt) {
            DB::statement($stmt);
        }

        DB::statement('ALTER TABLE `locations_temp` ADD PRIMARY KEY(`id`);');
        DB::statement('ALTER TABLE `locations_temp` ADD INDEX(`zip_code`);');

		DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
