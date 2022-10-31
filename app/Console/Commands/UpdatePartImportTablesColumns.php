<?php

namespace App\Console\Commands;

use App\Models\Column\Column;
use App\Models\Producer\Producer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;

class UpdatePartImportTablesColumns extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:PartImportTables';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete and recreate all part import tables and index tables';

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
        foreach (Producer::all() as $producer) {
            $columns = Column::where('import_parts_table', 1)->get();

            foreach (config('language') as $language) {
                $table = $producer->unique_id . '_parts_' . $language['key'];
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

}
