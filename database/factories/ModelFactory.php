<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(\App\Models\User::class, function (Faker\Generator $faker) {
    static $password;

    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = bcrypt('secret'),
        'remember_token' => str_random(10)
    ];
});

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\Company::class, function (Faker\Generator $faker) {
    $faker = Faker\Factory::create('de_DE');

    return [
        'user_id' => App\Models\User::where('email', 'premium@nmkr.at')->first()->id,
        'name' => $faker->company,
        'location_id' => App\Models\Location::inRandomOrder()->first()->id
    ];
});

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\Keyword::class, function (Faker\Generator $faker) {
    $faker = Faker\Factory::create('de_DE');

    return [
        'keyword' => $faker->word
    ];
});

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\CategoryEvent::class, function (Faker\Generator $faker) {
    $faker = Faker\Factory::create('de_DE');

    return [
        'name' => $faker->colorName,
        'description' => $faker->text
    ];
});

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\CategoryDeal::class, function (Faker\Generator $faker) {
    $faker = Faker\Factory::create('de_DE');

    return [
        'name' => $faker->colorName,
        'description' => $faker->text
    ];
});

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\Event::class, function (Faker\Generator $faker) {
    $faker = Faker\Factory::create('de_DE');

    $disc_type = $faker->randomElement(['none', 'fixed', 'percent']);
    if ($disc_type == 'none') {
        $disc_val = null;
    } elseif ($disc_type == 'fixed') {
        $disc_val = $faker->randomElement([100, 200, 500, 1000]);
    } else {
        $disc_val = $faker->randomElement([5, 10, 20, 30]);
    }

    return [
        'name' => $faker->sentence(),
        'summary' => $faker->sentence,
        'description' => $faker->text(1000),
        'date_start' => $faker->dateTimeBetween('-1 week', '+3 weeks'),
        'date_end' => $faker->dateTimeBetween('+1 month', '+1 year'),
        'time_start' => $faker->time($format = 'H:i:s', $max = 'now'),
        'time_end' => $faker->time($format = 'H:i:s', $max = 'now'),
        'regular_price' => $faker->numberBetween(100, 5000),
        'discount_type' => $disc_type,
        'discount_value' => $disc_val,
        'valid_until' => $faker->dateTimeBetween('+6 month', '+1 year'),
        'discount_coupon' => $faker->swiftBicNumber,
        'video_url' => 'https://www.youtube.com/watch?v=fwdwo6CGGW0',
        'product_url' => 'https://www.mercedes-benz.com/de/mercedes-benz/fahrzeuge/personenwagen/g-klasse/',
        'active' => 1,
        'approved' => 1,
        'category_event_id' => \App\Models\CategoryEvent::inRandomOrder()->first()->id,
        'location_id' => \App\Models\Location::inRandomOrder()->first()->id,
        'company_id' => \App\Models\Company::inRandomOrder()->first()->id,
    ];
});

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\Deal::class, function (Faker\Generator $faker) {
    $faker = Faker\Factory::create('de_DE');

    $disc_type = $faker->randomElement(['none', 'fixed', 'percent']);
    if ($disc_type == 'none') {
        $disc_val = null;
    } elseif ($disc_type == 'fixed') {
        $disc_val = $faker->randomElement([100, 200, 500, 1000]);
    } else {
        $disc_val = $faker->randomElement([5, 10, 20, 30]);
    }

    return [
        'name' => $faker->sentence(),
        'summary' => $faker->sentence,
        'description' => $faker->text(1000),
        'conditions' => $faker->text(1000),
        'date_start' => $faker->dateTimeBetween('-1 week', '+3 weeks'),
        'date_end' => $faker->dateTimeBetween('+1 month', '+1 year'),
        'regular_price' => $faker->numberBetween(100, 5000),
        'discount_type' => $disc_type,
        'discount_value' => $disc_val,
        'approved' => 1,
        'active' => 1,
        'video_url' => 'https://www.youtube.com/watch?v=fwdwo6CGGW0',
        'product_url' => 'https://www.mercedes-benz.com/de/mercedes-benz/fahrzeuge/personenwagen/g-klasse/',
        'discount_coupon' => $faker->swiftBicNumber,
        'category_deal_id' => \App\Models\CategoryDeal::inRandomOrder()->first()->id,
        'location_id' => \App\Models\Location::inRandomOrder()->first()->id,
        'company_id' => \App\Models\Company::inRandomOrder()->first()->id,
    ];
});

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\BlogPost::class, function (Faker\Generator $faker) {
    $faker = Faker\Factory::create('de_DE');

    return [
        'name' => $faker->sentence(),
        'description' => $faker->text(1000),
        'user_id' => \App\Models\User::whereIn('role', ['employee', 'admin'])->inRandomOrder()->first()->id,
        'directory_id' => \App\Models\Directory::inRandomOrder()->first()->id,
        'created_at' => $faker->dateTimeBetween('-1 year', 'today'),
    ];
});

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\Rating::class, function (Faker\Generator $faker) {
    $faker = Faker\Factory::create('de_DE');

    return [
        'name' => $faker->name,
        'title' => $faker->sentence,
        'description' => $faker->text(1000),
        'rating' => $faker->numberBetween(1, 5),
        'approved' => $faker->boolean(100),
        'is_visible' => $faker->boolean(100),
        'ip_address' => $faker->ipv4,
        'company_id' => \App\Models\Company::where('id', '<', 20)->inRandomOrder()->first()->id,
        'directory_id' => \App\Models\Directory::inRandomOrder()->first()->id,
    ];
});
