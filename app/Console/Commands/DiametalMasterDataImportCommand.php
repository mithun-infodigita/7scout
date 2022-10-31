<?php

namespace App\Console\Commands;


use App\Producers\Diametal\ImportScripts\DiametalBasicImport;
use App\Producers\Diametal\Jobs\DiametalMasterDataImportJob;
use Illuminate\Console\Command;

class DiametalMasterDataImportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'masterDataImport:Diametal';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Master Date import from Diametal';

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
     * @return int
     */
    public function handle()
    {
        DiametalMasterDataImportJob::dispatch();
     }

}
