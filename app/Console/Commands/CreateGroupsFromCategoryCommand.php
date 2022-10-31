<?php

namespace App\Console\Commands;

use App\Models\Category\Category;
use App\Models\Group\Group;
use App\Models\GroupColumn\GroupColumn;
use Illuminate\Console\Command;

class CreateGroupsFromCategoryCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rework:createGroupsFromCategory';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create Groups from Category';

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
        $categories = Category::all();

        foreach ($categories as $category) {

            if (!$category->group) {
                Group::create([
                    'name' => $category->name,
                    'de' => $category->de,
                    'en' => $category->en,
                    'fr' => $category->fr,
                    'it' => $category->it,
                    'category_id' => $category->id
                ]);
            }

        }
    }
}
