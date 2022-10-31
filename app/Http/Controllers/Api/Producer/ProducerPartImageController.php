<?php

namespace App\Http\Controllers\Api\Producer;

use App\Http\Controllers\Controller;

use App\Http\Resources\Api\File\FileCollectionResource;
use App\Models\Column\Column;
use App\Models\Producer\Producer;
use Illuminate\Http\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Arr;

class ProducerPartImageController extends Controller
{
    public function index(Request $request, Producer $producer)
    {
        $partImages = scandir(public_path('/storage/producers/'.$producer->unique_id.'/partImages'));

        $partImages = array_values(Arr::except($partImages , [0,1]));

        return response()->json($partImages);
    }


    public function store(Request $request, Producer $producer)
    {
        $fileName =  $request->file('file')->getClientOriginalName();

        if($producer->unique_id === 'voelkel') {
            $fileName = str_replace('ô','Ö', $fileName);
            $fileName = str_replace('Å','ü', $fileName);
            $fileName = str_replace('Ñ','ä', $fileName);
        }

        $request->file('file')->storeAs(
            $producer->unique_id.'/partImages/',
            $fileName,
            'producers'
        );

       return response('success', 200);
    }

    public function show(Request $request, Producer $producer, ... $id)
    {
        $partImage = $producer->getMedia('partImages')->where('id', $id[0])->first();

        return response()->download($partImage->getPath(), $partImage->file_name);
    }

    public function destroy(Request $request, Producer $producer,... $fileName)
    {
        if(unlink(public_path('/storage/producers/'.$producer->unique_id.'/partImages/'.$fileName[0]))) {
            return response('success', 200);
        }
    }

}
