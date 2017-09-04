<?php

namespace App\Console\Commands;

use App\Models\CategoryDeal;
use App\Models\CategoryEvent;
use App\Models\CategoryPrimary;
use App\Models\CategorySecondary;
use Illuminate\Console\Command;

ini_set('memory_limit', '1024M');

class GenerateCategoryCounts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:counts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // todo clear cache before, then cache after the command again.

        collect([
            CategoryPrimary::class,
            CategorySecondary::class,
            CategoryEvent::class,
            CategoryDeal::class
        ])->each(function($class) {

            (new $class)->orderBy('id')->chunk(100, function ($categories) {
                foreach ($categories as $index => $category) {
                    $count = $category->companies()->count();
                    $category->count = $count;
                    $category->save();
                }
            });

            $this->info($class . ' done..');
        });

//
//        /**
//         *
//         */
//        CategoryPrimary::all()->each(function($category) {
//             CategorySecondary::where('category_primary_id', $category->id)->orderBy('count', 'desc')->limit(16)->get()->each(function($subcategory) {
//                 $subcategory->featured = true;
//                 $subcategory->save();
//             });
//        });
//
//        $this->info('Featured Entries saved..');
//
//        /*collect([
//            'Baden-Württemberg',
//            'Bayern',
//            'Berlin',
//            'Brandenburg',
//            'Bremen',
//            'Hamburg',
//            'Hessen',
//            'Niedersachsen',
//            'Nordrhein-Westfalen',
//            'Rheinland-Pfalz',
//            'Saarland',
//            'Sachsen',
//            'Sachsen-Anhalt',
//            'Schleswig-Holstein',
//            'Thüringen',
//            'Mecklenburg-Vorpommern',
//        ])->each(function($ort) {
//                CategoryTownship::firstOrCreate([
//                    'name' => $ort,
//                    'slug' => str_slug($ort),
//                ]);
//        });
//
//        $this->info('Townships Created..');*/
//
//        /**
//         *
//         */
//        CategoryTownship::all()->each(function ($category) {
//            $count = Pool::where('bundesland', $category->name)->count();
//            $category->count = $count;
//            $category->save();
//        });
//
//        $this->info('Townships Categories done..');
//
//        /**
//         *
//         */
//        CategorySecondary::all()->each(function ($sub_category) {
//            $count = Pool::where('category_secondary_id', $sub_category->id)->count();
//
//            if($count > 100) {
//                // wir setzten 30% der Kunden bereits als PremiumKunden
//                $prozentual = round($count / 100 * 30);
//
//                Pool::where('category_secondary_id', $sub_category->id)
//                    ->limit($prozentual)
//                    ->inRandomOrder()
//                    ->each(function ($addr) {
//                       $addr->featured = 1;
//                       $addr->save();
//                    });
//
//                $this->info('Set Featured on: ' . $sub_category->name);
//            }
//        });
//
//        $this->info('Featured Pool Entries done..');
    }
}
