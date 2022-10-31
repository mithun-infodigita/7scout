<?php

namespace App\Http\Controllers\Api\PartIndex;

use Algolia\ScoutExtended\Facades\Algolia;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\PartIndex\PartIndexCollectionResource;
use App\Http\Resources\Api\PartIndex\PartIndexSingleResource;
use App\Models\Column\Column;
use App\Models\Facet\Facet;
use App\Models\PartIndex\PartIndex;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PartIndexController extends Controller
{
    public function index()
    {
        return response()->json(PartIndexCollectionResource::collection(PartIndex::all()));
    }

    public function show(Request $request, PartIndex $partIndex)
    {
        return response()->json(new PartIndexSingleResource($partIndex));
    }

    public function store(Request $request)
    {
        $request->merge(['status' => 'empty']);

        $partIndex = PartIndex::create($request->all());

        //$this->createModel($partIndex->model_name, $partIndex->table_name);

        //$this->createTable($partIndex->table_name);

        return response()->json(new PartIndexSingleResource($partIndex));
    }

    public function createModel($modelName, $tableName)
    {
        //todo set deployer to make this writeable

        $templateContent = file_get_contents(base_path('app/Models/Indices/Template.php'));

        $newIndex = fopen(storage_path('app/PartIndices/'.$modelName.".php"), "w+");

        $content = str_replace('Template', $modelName, $templateContent);

        $content = str_replace('part_index_table', $tableName, $content);

        $content = str_replace('index_name', $tableName, $content);

        $content = str_replace('test_index_name', 'test_'.$tableName, $content);

        fwrite($newIndex, $content);

        fclose($newIndex);
    }

    public function createTable($tableName)
    {
        $columns = Column::where('index_table', 1)->get();

        Schema::create($tableName, function($table) use($columns) {
            $table->id();

            foreach($columns as $column) {
                $type = $column->type;
                if($column->nullable){
                    $table->$type($column->name)->nullable();
                }
                else {
                    if($column->unique) {
                        $table->$type($column->name)->unique()->index();
                    }
                    else {
                        $table->$type($column->name);
                    }
                }
            }
            $table->timestamps();
        });
    }
//    public function createTable($tableName)
//    {
//        if(!Schema::hasTable($tableName)) {
//            Schema::create($tableName, function ($table) {
//                $table->id();
//                $table->string('part_id', 255)->unique()->index();
//                foreach (config('columns.part_index') as $column) {
//                    $table->text($column)->nullable();
//                }
//                $table->timestamps();
//            });
//        }
//    }

    public function truncate(Request $request, PartIndex $partIndex)
    {
        DB::table($partIndex->table_name)->truncate();

        $partIndex->update([
            'status' => 'empty'
        ]);

        return response(200);
    }

    public function importToAlgolia(Request $request, PartIndex $partIndex)
    {
        $className = "App\Models\Indices\\".$partIndex->model_name;

        $class = new $className();

        Artisan::call('scout:import' , ["model" => $class]);

        $facets = Facet::all()->load('column');

        $columns = $facets->map(function ($item, $key) {
            return $item->column->name;
        });

        $uniqueFacets = array_values($columns->unique()->toArray());

        foreach (config('algolia.standardFacets') as $facet)
        {
            if (!in_array($facet, $uniqueFacets, true)) {
                array_push($uniqueFacets, $facet);
            }
        }

        $searchableAttributes = config('algolia.searchableAttributes');

        foreach ($uniqueFacets as $facet) {
            if (!in_array($facet, $searchableAttributes, true)) {
                array_push($searchableAttributes, $facet);
            }
        }

        $index = Algolia::index($class);

        $index->setSettings([
            'attributeForDistinct' => "group_id",
            "distinct"=> true,
            "attributesForFaceting" => $uniqueFacets,
            'customRanking' => [
                'asc(sort_value)',
                'asc(overall_length_value)'
            ],
            "searchableAttributes" => $searchableAttributes,
            "disableTypoToleranceOnAttributes" => config('algolia.disableTypoToleranceOnAttributes')
        ]);

        return response(200);
    }
}

