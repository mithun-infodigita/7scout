<?php

namespace App\Console\Commands;


use Illuminate\Console\Command;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Http\Controllers\Api\Category\CacheCategoryController;

class CacheCategoriesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:categories';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cache categories';

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
        $casheCategoryController = new CacheCategoryController;
        $casheCategoryController->cacheCategoriesAsTree();

    }
}
