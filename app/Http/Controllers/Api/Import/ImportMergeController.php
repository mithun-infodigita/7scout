<?php

namespace App\Http\Controllers\Api\Import;

use App\Http\Controllers\Controller;
use App\Jobs\MergeRequest\MergeParts;
use App\Models\Import\Import;
use Illuminate\Http\Request;

class ImportMergeController extends Controller
{
    public function merge(Request $request, Import $import)
    {
        $import->update([
            'status'    => 'merging',
        ]);

        MergeParts::dispatch($import);

        return response('success', 200);
    }
}

