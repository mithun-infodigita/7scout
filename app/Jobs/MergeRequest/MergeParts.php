<?php

namespace App\Jobs\MergeRequest;

use App\Models\Column\Column;
use App\Models\Group\Group;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use DB;

class MergeParts

{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $import;

    public $datatable;

    public function __construct($import)
    {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', '3000000000000');

        $this->import = $import;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->setDataTable();

        $this->mergeParts();

        $this->import->update([
            'status'        => 'merged',
            'notification' =>  'Merged at '.Carbon::now()->format('d.m.Y H:i')
        ]);
    }

    public function setDataTable()
    {
        $this->datatable = $this->import->producer->unique_id."_parts_".$this->import->language;

    }


    public function mergeParts()
    {
        $import = $this->import;

        $producer = $import->producer;

        $partIndexModel = 'App\Models\Indices\\PartIndex'.ucfirst($import->language);
        $partIndexClass = new $partIndexModel();

        $parts = DB::table($this->datatable)->where('part_id', '!=', null)
            ->where('weight', '!=', NULL)

            ->where('preferential_beneficiary_eu', '!=', NULL)
            ->where('group_id', '!=', NULL)
            ->where('cat_level_1_id', '!=', NULL)
            ->where('group_name', '!=', NULL)
            ->where('customs_tariff_numbers', '!=', NULL)
            ->where('country_of_origin', '!=', NULL)
            ->where('price', '!=', NULL)
            ->where('stock', '!=', NULL)
            //->where('reprocurement_time', '>=', 0)
            ->get();


        $activeGroupIds = Group::where('active', true)->pluck('id')->toArray();

        foreach ($parts as $part) {

            if(in_array($part->group_id, $activeGroupIds)) {
                DB::table($this->datatable)
                    ->where('part_id', $part->part_id)
                    ->update(['merged' => Carbon::now()->toDateTimeString()]);
                $partIndexClass::updateOrCreate(
                    ['part_id' => $part->part_id],
                    $this->getColumns($part)
                );
            }
        }

        $partsToDelete = $partIndexClass::whereNotIn('part_id', $parts->pluck('part_id'))->where('producer_id', $this->import->producer->unique_id)->get();

        foreach ($partsToDelete as $part) {

            $part->delete();
        }
    }

    public function getColumns($part)
    {
        $basicColumns = Column::where('index_table', 1)->get();

        $columns = [];

        foreach ($basicColumns as $column) {
            $columnName = $column->name;
            $columns[$column->name] = $part->$columnName;
        }

        return $columns;
    }
}
