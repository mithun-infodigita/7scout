<?php

namespace App\Jobs\Import;


use App\Events\Import\ImportCompletedEvent;
use App\Imports\AdditionalImport\MapImportRules;
use App\Imports\PriceImport\MapImportPriceRules;
use App\Models\Category\Category;
use App\Models\Group\Group;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class BasicImportJob implements ShouldQueue

{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 3600;

    public $import;

    public function __construct($import)
    {
        $this->import = $import;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->basicImport($this->import);
        $this->import->update([
            'status'        =>  'imported',
            'notification'  =>  'Successfully imported '. Carbon::now()->format('d.m.Y H:i')
        ]);

        event(new ImportCompletedEvent($this->import));
    }


    public function basicImport($import)
    {

        $class = 'App\Imports\BasicImport\\'.$import->producer->unique_id.'_basic_import';
        $basicImport = new $class();
        $basicImport->basicImport($import);


        if(count(json_decode($import->one_to_one))) {
            $this->mapOneToOne($import);
        }

        if(count(json_decode($import->group_mapping))) {
            $this->mapGroups($import);
        }

        if(count(json_decode($import->category_mapping))) {
            $this->mapCategories($import);
        }

        if(count($import->importRules)) {
            $mapImportRules = new MapImportRules();
            $mapImportRules->mapData($import);
        }

        if(count($import->importPriceRules)) {
           ImportPriceJob::dispatch($import);
        }
    }


    public function mapOneToOne($import)
    {
        $table = $import->producer->unique_id . '_parts_'.$import->language;

        DB::table($table)->chunkById(10000000, function ($parts) use ($table, $import) {
            foreach ($parts as $part) {
                DB::table($table)
                    ->where('id', $part->id)
                    ->update($this->getOneToOneColumns($import, $part));
            }
        });
    }

    public function getOneToOneColumns($import,$part)
    {
        $oneToOne = json_decode($import->one_to_one);

        $source = json_decode($part->full_record);

        $data_1 = json_decode($part->data_1, true);

        $columns = [];
        foreach ($oneToOne as $item) {
            $columns[$item->column] = eval("return ".$item->map_script.";");
        }

        return $columns;
    }

    public function mapGroups($import)
    {
        $table = $import->producer->unique_id . '_parts_'.$import->language;

        DB::table($table)->chunkById(10000000, function ($parts) use ($table, $import) {

            foreach ($parts as $part) {
                $source = json_decode($part->full_record);

                foreach (json_decode($import->group_mapping) as $item) {
                    if (Str::contains(eval("return " . $item->map_script . ";"), $item->validation_string)) {
                        DB::table($table)
                            ->where('id', $part->id)
                            ->update([
                                'group_id'  =>  $item->group_id,
                                'group_name'  =>  Group::find($item->group_id)->name
                            ]);
                    }
                }

            }
        });
    }


    public function mapCategories($import)
    {
        $table = $import->producer->unique_id . '_parts_'.$import->language;

        DB::table($table)->chunkById(10000000, function ($parts) use ($table, $import) {

            foreach ($parts as $part) {

                $source = json_decode($part->full_record);

                foreach (json_decode($import->category_mapping) as $item) {

                    if (Str::contains(eval("return " . $item->map_script . ";"), $item->validation_string)) {
                        DB::table($table)
                            ->where('id', $part->id)
                            ->update($this->categoryColumns($item, $import));
                    }
                }

            }
        });
    }

    public function categoryColumns($item, $import)
    {
        $columns = [];

        $category = Category::find($item->category_id);

        $catLevel = $category->level + 1;

        $language = $import->language;

        $columns['cat_level_1_id'] = null;
        $columns['cat_level_1_name'] = null;
        $columns['cat_level_2_id'] = null;
        $columns['cat_level_3_name'] = null;
        $columns['cat_level_3_id'] = null;
        $columns['cat_level_3_name'] = null;
        $columns['cat_level_4_id'] = null;
        $columns['cat_level_4_name'] = null;
        $columns['cat_level_5_id'] = null;
        $columns['cat_level_5_name'] = null;


        $columns['cat_level_'.$catLevel.'_id'] =  $category->id;

        if( $columns['cat_level_5_id']) {
            $columns['cat_level_5_name'] = Category::where('id', $columns['cat_level_5_id'])->first()->$language;
            $parentId = Category::where('id', $columns['cat_level_5_id'])->first()->parent_id;
            $parent = Category::where('id', $parentId)->first();
            $columns['cat_level_4_id'] = $parentId;
            $columns['cat_level_4_name'] = $parent->$language;
            $group = Group::where('category_id', $columns['cat_level_5_id'])->first();
            $columns['group_id'] = $group->id;
            $columns['group_name'] = $group->$language;
        }

        if($columns['cat_level_4_id']) {
            $columns['cat_level_4_name'] = Category::where('id', $columns['cat_level_4_id'])->first()->$language;
            $parentId = Category::where('id', $columns['cat_level_4_id'])->first()->parent_id;
            $parent = Category::where('id', $parentId)->first();
            $columns['cat_level_3_id'] = $parentId;
            $columns['cat_level_3_name'] = $parent->$language;
            if(! array_key_exists('group_id',$columns)) {
                $group = Group::where('category_id', $columns['cat_level_4_id'])->first();
                $columns['group_id'] = $group->id;
                $columns['group_name'] = $group->$language;
            }
        }

        if($columns['cat_level_3_id']) {
            $columns['cat_level_3_name'] = Category::where('id', $columns['cat_level_3_id'])->first()->$language;
            $parentId = Category::where('id', $columns['cat_level_3_id'])->first()->parent_id;
            $parent = Category::where('id', $parentId)->first();
            $columns['cat_level_2_id'] = $parentId;
            $columns['cat_level_2_name'] = $parent->$language;
            if(! array_key_exists('group_id',$columns)) {
                $group = Group::where('category_id', $columns['cat_level_3_id'])->first();
                $columns['group_id'] = $group->id;
                $columns['group_name'] = $group->$language;
            }
        }

        if($columns['cat_level_2_id']) {
            $columns['cat_level_2_name'] = Category::where('id', $columns['cat_level_2_id'])->first()->$language;
            $parentId = Category::where('id', $columns['cat_level_2_id'])->first()->parent_id;
            $parent = Category::where('id', $parentId)->first();
            $columns['cat_level_1_id'] = $parentId;
            $columns['cat_level_1_name'] = $parent->$language;
            if(! array_key_exists('group_id',$columns)) {
                $group = Group::where('category_id', $columns['cat_level_2_id'])->first();
                $columns['group_id'] = $group->id;
                $columns['group_name'] = $group->$language;
            }
        }

        if(! array_key_exists('group_id',$columns)) {
            $group = Group::where('category_id', $columns['cat_level_1_id'])->first();
            $columns['group_id'] = $group->id;
            $columns['group_name'] = $group->$language;
        }

        return $columns;
    }

    public function mapPrices($import)
    {
        $table = $import->producer->unique_id . '_parts_'.$import->language;

        DB::table($table)->chunkById(10000000, function ($parts) use ($table, $import) {

            foreach ($parts as $part) {

                $source = json_decode($part->full_record);

                $prices = [];

                foreach (json_decode($import->price_mapping) as $item) {
                    if(!empty($item->map_script)) {
                        $prices[$item->currency] = eval("return " . $item->map_script . ";");
                    }
                }

                DB::table($table)
                    ->where('id', $part->id)
                    ->update([
                        'prices'    =>      json_encode($prices)
                    ]);

            }
        });
    }

    public function mapAllLanguages($import)
    {
        foreach (config('language')['languages'] as $language) {
            $table = $import->producer->unique_id . '_parts_'.$language['key'];

            foreach ($import->importRules as $rule) {
                switch ($rule->rule_type) {
                    case 'fix_text':
                        $this->mapFixText($import, $rule, $table, $language['key'], strtoupper($language['key']));
                        break;
                }
            }
        }
    }

    public function mapSingleLanguage($import)
    {

        $table = $import->producer->unique_id . '_parts_'.$import->language;

        foreach ($import->importRules as $rule) {
            switch ($rule->rule_type) {
                case 'map_text':
                    $this->mapText($import, $rule, $table );
                    break;
            }
        }

    }

    public function mapText($import, $rule, $table)
    {
        $sourceColumn = $rule->source_column;

        $parts = DB::table($table)->get();

        foreach ($parts as $part) {

            $source = json_decode($part->$sourceColumn);

            DB::table($table)
                ->where('id', $part->id)
                ->update([
                    'part_name' =>      eval("return ".$rule->map_value_script.";")
                ]);
        }
    }

}
