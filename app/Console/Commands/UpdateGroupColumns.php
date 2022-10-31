<?php

namespace App\Console\Commands;

use App\Jobs\Category\CacheCategoryJob;
use App\Models\Column\Column;
use App\Models\Group\Group;
use App\Models\GroupColumn\GroupColumn;
use Illuminate\Console\Command;

class UpdateGroupColumns extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:GroupColumns';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update missing translations';

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

        $columns = Column::where('default_show_in_frontend', true)->get();

        $groups = Group::all();

        foreach ($groups as $group) {

            foreach ($columns as $column) {

                $groupColumn = GroupColumn::where('group_id', $group->id)->where('column_id', $column->id)->first();

                if($groupColumn) {
                    $groupColumn->name      = $column->default_de_name;
                    $groupColumn->order_column = $column->order_column;
                    $groupColumn->de_table  = $groupColumn->de_table ? : $column->default_de_name;
                    $groupColumn->en_table  = $groupColumn->en_table ? : $column->default_en_name;
                    $groupColumn->fr_table  = $groupColumn->fr_table ? : $column->default_fr_name;
                    $groupColumn->it_table  = $groupColumn->it_table ? : $column->default_it_name;
                    $groupColumn->de_detail  = $groupColumn->de_detail ? : $column->default_de_name;
                    $groupColumn->en_detail  = $groupColumn->en_detail ? : $column->default_en_name;
                    $groupColumn->fr_detail  = $groupColumn->fr_detail ? : $column->default_fr_name;
                    $groupColumn->it_detail  = $groupColumn->it_detail ? : $column->default_it_name;
                    $groupColumn->save();
                }
                else {
                    $groupColumn = new GroupColumn();
                    $groupColumn->order_column = $column->order_column;
                    $groupColumn->column_id = $column->id;
                    $groupColumn->group_id  = $group->id;
                    $groupColumn->name      = $column->default_de_name;
                    $groupColumn->de_table  = $column->default_de_name;
                    $groupColumn->en_table  = $column->default_en_name;
                    $groupColumn->fr_table  = $column->default_fr_name;
                    $groupColumn->it_table  = $column->default_it_name;
                    $groupColumn->de_detail  = $column->default_de_name;
                    $groupColumn->en_detail  = $column->default_en_name;
                    $groupColumn->fr_detail  = $column->default_fr_name;
                    $groupColumn->it_detail  = $column->default_it_name;
                    $groupColumn->show_in_table  = $column->default_show_in_table;
                    $groupColumn->show_in_table_detail  = $column->default_show_in_table_detail;
                    $groupColumn->show_in_detail_page   = $column->default_show_in_detail_page;
                    $groupColumn->save();
                }

            }

        }

        CacheCategoryJob::dispatch();
    }
}
