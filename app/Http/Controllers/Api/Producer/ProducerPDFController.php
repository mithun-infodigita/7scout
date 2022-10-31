<?php

namespace App\Http\Controllers\Api\Producer;

use App\Http\Controllers\Controller;
use App\Models\Producer\Producer;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;


class ProducerPDFController extends Controller
{
    public function index(Request $request, Producer $producer)
    {
        $pdfs = scandir(public_path('/storage/producers/'.$producer->unique_id.'/pdfs'));

        $pdfs = array_values(Arr::except($pdfs , [0,1]));

        return response()->json($pdfs);
    }

    public function store(Request $request, Producer $producer)
    {
        $request->file('files')->storeAs(
            $producer->unique_id.'/pdfs/',
            $request->file('files')->getClientOriginalName(),
            'producers'
        );

        return response('success', 200);
    }

    public function destroy(Request $request, Producer $producer, ... $fileName)
    {
        if(unlink(public_path('/storage/producers/'.$producer->unique_id.'/pdfs/'.$fileName[0]))) {
            return response('success', 200);
        }
    }
}

