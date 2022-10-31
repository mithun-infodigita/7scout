<?php

namespace App\Http\Controllers\Api\Import;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Import\StoreImportRequest;
use App\Http\Requests\Api\Import\UpdateImportRequest;
use App\Http\Resources\Api\Import\ImportCollectionResource;
use App\Http\Resources\Api\Import\ImportSingleResource;
use App\Models\Import\Import;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class  ImportController extends Controller
{
    public function index(Request $request)
    {
        return response()->json(ImportCollectionResource::collection(Import::all()));
    }

    public function store(StoreImportRequest $request)
    {
        $request->merge([
            'status' => 'draft',
            'one_to_one'    =>      json_encode([]),
            'category_mapping'=>      json_encode([]),
            'price_mapping' =>      json_encode([]),
            'pdf_mapping' =>      json_encode([])
        ]);

        $import = Import::create($request->all());

        return response()->json(new ImportSingleResource($import));
    }

    public function show(Request $request, Import $import)
    {
        return response()->json(new ImportSingleResource($import));
    }

    public function update(UpdateImportRequest $request, Import $import)
    {
        $import->update($request->all());

        return response()->json(new ImportSingleResource($import->fresh()));
    }

    public function duplicateImport(Request $request, Import $import)
    {
        $import->load('importRules');

        $newImport = $import->replicate();

        $newImport->notification = 'Copied from '.$newImport->name;
        $newImport->name = $newImport->name." - copy";
        $newImport->status = 'draft';

        $newImport->push();

        $this->replicateRelations($import, $newImport);

        return response()->json(new ImportSingleResource($newImport));
    }

    public function replicateRelations($import, $newImport) {
        foreach ($import->importRules as $importRule) {
            unset($importRule->id);
            $newImport->importRules()->create($importRule->toArray());
        }
    }

    public function destroy(Request $request, Import $import)
    {
        foreach ($import->getMedia() as $media) {
            $media->delete();
        }

        foreach ($import->getMedia('additionalFiles') as $media) {
            $media->delete();
        }

        foreach ($import->getMedia('priceFiles') as $media) {
            $media->delete();
        }

        foreach ($import->importRules as $importRule) {
            $importRule->delete();
        }
        Schema::dropIfExists('import_parts_'.$import->id);

        $import->delete();
    }
}

