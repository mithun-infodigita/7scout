<?php

namespace App\Console\Commands;

use App\Models\Column\Column;
use App\Models\Producer\Producer;
use Illuminate\Console\Command;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateColumnTypes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:ColumnTypes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update column types';

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
            $table = 'part_index_' . $language['key'];
            $tableName = 'part_index_' . $language['key'];

            Schema::table($table, function (Blueprint $table) use ($tableName, $columns) {

                foreach ($columns as $column) {

                    $type = $column->type;

                    if (Schema::hasColumn($tableName, $column->name)) {
                        if ($type === 'text') {
                            if ($column->nullable) {
                                $table->$type($column->name)->nullable()->change();
                            } else {
                                if ($column->unique) {
                                    $table->$type($column->name)->unique()->index()->change();
                                } else {
                                    $table->$type($column->name)->change();
                                }
                            }
                        }
                    }
                }
            });
        }

        foreach (Producer::all() as $producer) {
            $columns = Column::where('import_parts_table', 1)->get();

            foreach (config('language') as $language) {
                $table = $producer->unique_id . '_parts_' . $language['key'];
                $tableName = $producer->unique_id . '_parts_' . $language['key'];

                Schema::table($table, function (Blueprint $table) use ($tableName, $columns) {

                foreach ($columns as $column) {
                    $type = $column->type;

                    if (Schema::hasColumn($tableName, $column->name)) {
                        if ($type === 'text') {

                            if ($column->nullable) {
                                $table->text($column->name)->nullable()->change();
                            } else {
                                if ($column->unique) {
                                    $table->$type($column->name)->unique()->index()->change();
                                } else {
                                    $table->$type($column->name)->change();
                                }
                            }
                        }
                    }
                }
                });
            }
        }
    }
}
