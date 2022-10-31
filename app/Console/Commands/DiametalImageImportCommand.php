<?php

namespace App\Console\Commands;

use App\Producers\Amf\StockUpdate\AmfStockUpdate;
use App\Producers\Diametal\Jobs\DiametalImageImportJob;
use App\Producers\Diebold\StockUpdate\DieboldStockUpdate;
use Illuminate\Console\Command;

class DiametalImageImportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'imageImport:Diametal';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Image import from Diametal';

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
        DiametalImageImportJob::dispatch();
     }

}
