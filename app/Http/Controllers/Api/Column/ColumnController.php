<?php

namespace App\Http\Controllers\Api\Column;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Column\StoreColumnRequest;
use App\Http\Requests\Api\Column\UpdateColumnRequest;
use App\Http\Resources\Api\Column\ColumnCollectionResource;
use App\Http\Resources\Api\Column\ColumnSingleResource;
use App\Models\Column\Column;
use App\Models\Import\Import;
use App\Models\PartIndex\PartIndex;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Artisan;

class ColumnController extends Controller
{
    public function index(Request $request)
    {
        return response()->json(ColumnCollectionResource::collection(Column::all()->sortBy('order_column')));
    }

    public function store(StoreColumnRequest $request)
    {
        $column = Column::create($request->all());

        $this->updateImports($column);

        $this->updateIndexes($column);

        return response()->json(new ColumnSingleResource($column));
    }

    public function show(Request $request, Column $column)
    {
        return response()->json(new ColumnSingleResource($column));
    }

    public function update(UpdateColumnRequest $request, Column $column)
    {
        $column->update($request->all());

        return response()->json(new ColumnSingleResource($column->fresh()));
    }

    public function updateOrder(Request $request)
    {
        Column::setNewOrder($request->columnIds);
    }

    public function destroy(Request $request, Column $column) {

        if($column->delete()) {
            return response('success', 200);
        }
        else {
            return response(["message" => "Group can't be deleted!"], 422);
        }
    }

    public function updateImports($column)
    {
        $imports = Import::all();

        foreach ($imports as $import)  {
            $table = $import->producer->unique_id."_parts_".$import->language;
            if (!Schema::hasColumn($table, $column->name)) {
                Schema::table($table, function (Blueprint $table) use ($column) {
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
                });
            }
        }
    }

    public function updateIndexes($column)
    {
        $indexes = PartIndex::all();

        foreach ($indexes as $index)  {
            $table = $index->table_name;
            if (!Schema::hasColumn($table, $column->name)) {
                Schema::table($table, function (Blueprint $table) use ($column) {
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
                });
            }
        }
    }

    public function updateGroupColumns()
    {
        Artisan::call('update:GroupColumns ');
    }
}

