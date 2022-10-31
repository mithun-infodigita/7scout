<?php

namespace App\Console\Commands;

use App\Producers\Amf\StockUpdate\AmfStockUpdate;
use App\Producers\Diebold\StockUpdate\DieboldStockUpdate;
use Illuminate\Console\Command;

class AmfStockUpdateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stockUpdate:amf';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatic stock update from Amf';

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
        $stockUpdate = new AmfStockUpdate();

        $stockUpdate->update();
     }

}
