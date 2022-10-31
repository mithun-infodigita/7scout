<?php

namespace App\Http\Controllers\Api\GlobalFeature\TableFilter;


use App\Http\Controllers\Controller;

use App\Http\Requests\Api\GlobalFeature\TableFilter\StoreTableFilterRequest;
use App\Http\Requests\Api\GlobalFeature\TableFilter\UpdateTableFilterRequest;
use App\Models\GlobalFeature\TableFilter\TableFilter;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class TableFilterController extends Controller
{

    public function index(Request $request)
    {
        return response()->json($request->user()->tableFilters->where('company_id', $request->user()->company_id)->groupBy('table_id'));
    }

    public function store(StoreTableFilterRequest $request)
    {
        $tableFilter = $request->user()->tableFilters()->create([
            'filter_data'   => json_encode($request->filter_data),
            'name'          => $request->name,
            'table_id'      => $request->table_id,
            'color'         => $request->color,
            'default'       => $request->default ? 1 : 0,
            'header_button' =>  $request->header_button ? 1 : 0,
        ]);

        if($request->default == 1) {
            $this->removeDefaults($tableFilter);
        }

        return $tableFilter;
    }

    public function update(UpdateTableFilterRequest $request, TableFilter $tableFilter)
    {
        $tableFilter->update([
            'filter_data'   => json_encode($request->filter_data),
            'name'          => $request->name,
            'table_id'      => $request->table_id,
            'color'         => $request->color,
            'default'       => $request->default ? 1 : 0,
            'header_button' =>  $request->header_button ? 1 : 0,
        ]);

        if($request->default == 1) {
            $this->removeDefaults($tableFilter);
        }

        return $request->user()->tableFilters;
    }

    public function destroy(Request $request, TableFilter $tableFilter)
    {
        $tableFilter->delete();

        return $request->user()->tableFilters;
    }

    public function removeDefaults($tableFilter)
    {
        $tableFilters = TableFilter::where('table_id', $tableFilter->table_id)
            ->where('id','<>', $tableFilter->id)
            ->get();

        foreach ($tableFilters as $filter) {
            $filter->update([
                'default'   => 0
            ]);
        }
    }
}
