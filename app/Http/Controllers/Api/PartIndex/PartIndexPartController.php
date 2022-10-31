<?php

namespace App\Http\Controllers\Api\PartIndex;

use App\Http\Controllers\Controller;
use App\Models\Import\Import;
use Illuminate\Http\Request;

class PartIndexPartController extends Controller
{

    public $import;

    public $datatable;


    public function deletePartsFromIndex(Request $request, Import $import)
    {
        $this->import = $import;

        $this->datatable = $this->import->producer->unique_id."_parts_".$this->import->language;

        $partIndexModel = 'App\Models\Indices\\PartIndex'.ucfirst($import->language);
        $partIndexClass = new $partIndexModel();

        $parts = $partIndexClass::where('producer_id', $this->import->producer->unique_id)->get();

        foreach ($parts as $part) {
           $part->delete();
        }

    }


}

