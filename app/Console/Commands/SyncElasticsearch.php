<?php

namespace App\Console\Commands;

use App\Models\Company;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Sleimanx2\Plastic\Facades\Plastic;

ini_set('memory_limit', '2048M');

class SyncElasticsearch extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:elasticsearch';

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
        // run mapper
        Artisan::call('mapping:rerun');

        Company::active()->orderBy('id')->chunk(20000, function($addresses)
        {
            $this->info('processing 20.000 - ' . Carbon::now()->format('d.m.Y H:i:s'));

            Plastic::persist()->bulkSave($addresses);

        });

        $this->info('import done');

        // categories?
        // events, deals?
    }
}
