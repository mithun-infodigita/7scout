<?php

namespace App\Console\Commands;

use App\Models\Column\Column;
use App\Models\Producer\Producer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;

class UpdateIndexPartTablesColumns extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:IndexPartTables';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete and recreate all index part tables and index tables';

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

        $columns = Column::where('index_table', 1)->get();

        foreach (config('language') as $language) {
            if(env('INDEX_APPENDIX') !== null) {
                $table = 'part_index_' . $language['key'].'_'.env('INDEX_APPENDIX');
            }
            else {
                $table = 'part_index_' . $language['key'];
            }

            Schema::dropIfExists($table);

            Schema::create($table, function ($table) use ($columns) {
                $table->id();

                foreach ($columns as $column) {
                    $type = $column->type;
                    if ($column->nullable) {
                        $table->$type($column->name)->nullable();
                    } else {
                        if ($column->unique) {
                            $table->$type($column->name)->unique()->index();
                        } else {
                            $table->$type($column->name);
                        }
                    }
                }
                $table->timestamps();
            });
        }

    }

}
