<?php

namespace App\Console\Commands;

use App\Producers\Diebold\StockUpdate\DieboldStockUpdate;
use Illuminate\Console\Command;

class DieboldStockUpdateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stockUpdate:diebold';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatic stock update from Diebold';

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
        $stockUpdate = new DieboldStockUpdate();

        $stockUpdate->update();
     }

}
