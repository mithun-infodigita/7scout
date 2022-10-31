<?php

namespace App\Console\Commands;

use App\Models\Group\Group;
use App\Models\GroupColumn\GroupColumn;
use Illuminate\Console\Command;

class DeleteGroupsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rework:deleteGroups';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove groups and create from category.';

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
        GroupColumn::truncate();

        Group::truncate();
    }
}
