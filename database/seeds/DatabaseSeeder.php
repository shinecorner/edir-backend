<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	//Location seeder should be called before/within import:addr
        // $this->call(LocationSeeder::class);

		DB::table('users')->delete();

		App\Models\User::create([
			'email' => 'admin@nmkr.at',
			'password' => bcrypt('changeme'),
            'title' => null,
            'gender' => 'Herr',
            'first_name' => 'Max',
            'last_name' => 'Mustermann',
            'role' => 'admin'
		]);

		App\Models\User::create([
			'email' => 'employee@nmkr.at',
			'password' => bcrypt('changeme'),
            'title' => null,
            'gender' => 'Herr',
            'first_name' => 'Max',
            'last_name' => 'Mustermann',
            'role' => 'employee'
		]);

		App\Models\User::create([
			'email' => 'kunde@nmkr.at',
			'password' => bcrypt('changeme'),
            'title' => null,
            'gender' => 'Herr',
            'first_name' => 'Max',
            'last_name' => 'Mustermann',
			'client_number' => rand(123123,123123123),
            'role' => 'customer'
		]);

		App\Models\User::create([
			'email' => 'premium@nmkr.at',
			'password' => bcrypt('changeme'),
            'title' => null,
            'gender' => 'Herr',
            'first_name' => 'Max',
            'last_name' => 'Mustermann',
			'client_number' => rand(123123,123123123),
            'role' => 'customer'
		]);

		DB::table('directories')->delete();
		App\Models\Directory::create([
			'name' => 'edir_1',
			'api_token' => App\Models\Directory::generateToken(),
		]);
		App\Models\Directory::create([
			'name' => 'edir_2',
			'api_token' => App\Models\Directory::generateToken(),
		]);


		DB::table('category_events')->delete();
		factory(\App\Models\CategoryEvent::class, 10)->create();
		DB::table('category_deals')->delete();
		factory(\App\Models\CategoryDeal::class, 10)->create();
		DB::table('events')->truncate();
		factory(\App\Models\Event::class, 200)->create();
		DB::table('deals')->truncate();
		factory(\App\Models\Deal::class, 200)->create();
		DB::table('blog_posts')->truncate();
		factory(\App\Models\BlogPost::class, 300)->create();
		DB::table('ratings')->truncate();
		factory(\App\Models\Rating::class, 200)->create();

		//create some files
		DB::table('images')->delete();
		$imageResize = new \App\Services\ImageResize;
		if (!file_exists(storage_path() . '/app/public/fakertmp/')) {
			mkdir(storage_path() . '/app/public/fakertmp/', 0777, true);
		}
		$file = storage_path() . '/app/public/fakertmp/test.jpg';
		if(!file_exists($file)) {
			$image = file_put_contents($file, file_get_contents('http://placehold.it/1920.png'));
		}

	}
}
