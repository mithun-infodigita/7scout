<?php

namespace App\Http\Controllers\Api\Import;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Group\StoreGroupRequest;
use App\Http\Resources\Api\Group\GroupCollectionResource;
use App\Http\Resources\Api\Group\GroupSingleResource;

use App\Http\Resources\Api\Import\ImportCollectionResource;
use App\Http\Resources\Api\Import\ImportSingleResource;
use App\Models\Group\Group;
use App\Models\Import\Import;
use App\Producers\Dixi\Models\DixiPartsDe;
use App\Producers\Dixi\Models\DixiPartsFr;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ImportPartDataController extends Controller
{
    public function index(Request $request, Import $import)
    {
        ini_set('memory_limit', '-1');

        $data = DB::table($import->producer->unique_id . '_parts_'.$import->language)->get();

        return response()->json($data);
    }

    public function show(Request $request, Import $import, $id)
    {
        $data = DB::table($import->producer->unique_id . '_parts_'.$import->language)->where('id', $id)->first();

        return response()->json($data);
    }

    public function truncate(Request $request, Import $import)
    {
        DB::table($import->producer->unique_id . '_parts_'.$import->language)->truncate();

        $import->update([
            'status'        =>  'empty',
            'notification'  =>  'Table truncated '. Carbon::now()->format('d.m.Y H:i')
        ]);
    }
}
