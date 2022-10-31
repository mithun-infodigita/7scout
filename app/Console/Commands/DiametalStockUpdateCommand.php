<?php

namespace App\Console\Commands;

use App\Producers\Diametal\StockUpdate\DiametalStockUpdate;
use Illuminate\Console\Command;

class DiametalStockUpdateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stockUpdate:diametal';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatic stock update from Diametal';

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
        $stockUpdate = new DiametalStockUpdate();

        $stockUpdate->update();
     }

}
