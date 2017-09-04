<?php

namespace App\Console\Commands;

use App\Models\CategoryPrimary;
use App\Models\CategorySecondary;
use App\Models\Company;
use App\Models\Location;
use App\Models\Pool;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

ini_set('memory_limit', '1024M');

class ImportAddresses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:addr';

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
    	$chunk_size = 25000;

		$this->info('Seeding locations...');

		$this->call('db:seed', [
			'--class' => 'LocationSeeder'
		]);

        // Poolglobal
        $this->info('Importing pool_global.sql...');

        // Note: these dump files must be generated with DELETE (or TRUNCATE) + INSERT statements
        $sql = file_get_contents(
            base_path() . DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR . 'seeds'
                        . DIRECTORY_SEPARATOR . 'dumps' . DIRECTORY_SEPARATOR . 'pool_global.sql'
        );

        // split the statements, so DB::statement can execute them.
        $statements = array_filter(array_map('trim', explode(';', $sql)));

        foreach ($statements as $stmt) {
            DB::statement($stmt);
        }

        $this->info('Import data into companies and locations table...');
		$this->info('Processing chunks of '. $chunk_size);
		$this->output->progressStart(ceil(DB::table('pool_global')->count()/$chunk_size));

        DB::table('pool_global')->orderBy('pool_global_id')->chunk($chunk_size, function ($addresses) {

            foreach ($addresses as $addr) {

                $company = new Company([
                    'name' => $addr->firmenname,
                    'email' => $addr->email,
                    'phone' => $addr->telefon,
                    'mobile' => $addr->mobil,
                    'fax' => $addr->telefax,
                    'www' => $addr->website,
                    'listing_level' => 'basic',
                    'listing_status' => 1,
                    'listing_valid_until' => '2020-01-01'
                ]);

                $temp_location = DB::table('locations_temp')->where('zip_code', $addr->plz)->first();

                $location  = Location::create([
                	'street_name' => trim(substr($addr->anschrift, 0, strripos($addr->anschrift, ' '))),
                	'street_additional' => null,
                	'street_number' => trim(substr($addr->anschrift, strripos($addr->anschrift, ' '))),
                	'zip_code' => $addr->plz,
                	'city' => $addr->ort,
                	'district' => $addr->regierungsbezirk,
                	'county' => $addr->landkreis,
                	'state' => $addr->bundesland,
                	'latitude' => $temp_location->latitude,
                	'longitude' => $temp_location->longitude,
				]);

                $company->location()->associate($location);
                $company->save();

                $primaryCategory = CategoryPrimary::firstOrCreate([
                    'name' => $addr->hauptkategorie,
                ]);

                $secondaryCategory = CategorySecondary::firstOrCreate([
                    'name' => $addr->unterkategorie,
                    'category_primary_id' => $primaryCategory->id
                ]);
                $secondaryCategory->parent()->associate($primaryCategory);


                $company->categories()->save($secondaryCategory, ['category_primary_id' => $primaryCategory->id]);
            }

            $this->output->progressAdvance();

        });

		$this->output->progressFinish();

		$this->info('Removing pool_global, locations_temp...');
        DB::statement('drop table pool_global');
        DB::statement('drop table locations_temp');

        $this->info('Import done');
    }
}
