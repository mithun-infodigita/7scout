<?php

namespace App\Http\Controllers\Api\Producer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Producer\StoreProducerRequest;
use App\Http\Requests\Api\Producer\UpdateProducerRequest;
use App\Http\Resources\Api\Producer\ProducerCollectionResource;
use App\Http\Resources\Api\Producer\ProducerSingleResource;
use App\Models\Column\Column;
use App\Models\Import\Import;
use App\Models\Producer\Producer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class ProducerController extends Controller
{
    public function index()
    {
        return response()->json(ProducerCollectionResource::collection(Producer::all()));
    }

    public function show(Request $request, Producer $producer)
    {
        return response()->json(new ProducerSingleResource($producer));
    }

    public function store(StoreProducerRequest $request)
    {
        $producer = Producer::create($request->all());

        $producer->dispatchLocations()->sync($request->dispatch_location_ids);

        $path = public_path('/storage/producers/'.$producer->unique_id);

        // if(!file_exists($path)) {
        //     mkdir($path);
        // }

        // $path = public_path('/storage/producers/'.$producer->unique_id.'/pdfs');

        // if(!file_exists($path)) {
        //     mkdir($path);
        // }


        if(!file_exists($path)) {
            // mkdir($path);
            Storage::disk('producers')->makeDirectory($producer->unique_id);
        }

        $path = public_path('/storage/producers/'.$producer->unique_id.'/pdfs');

        if(!file_exists($path)) {
            Storage::disk('producers')->makeDirectory($producer->unique_id.'/pdfs');
        }


        $this->createTables($producer);

        $this->createBasicImports($producer);

        return response()->json(new ProducerSingleResource($producer->fresh()));
    }

    public function update(UpdateProducerRequest $request, Producer $producer)
    {
        $producer->update($request->only(['name', 'active', 'unique_id']));

        $producer->dispatchLocations()->sync($request->dispatch_location_ids);

        return response()->json(new ProducerSingleResource($producer->fresh()));
    }

    public function createTables($producer)
    {
        $columns = Column::where('import_parts_table', 1)->get();

        foreach (config('language')as $language) {
            $table = $producer->unique_id . '_parts_' . $language['key'];
            if (!Schema::hasTable($table)) {
                Schema::create($table, function ($table) use ($columns) {
                    $table->id();

                    foreach ($columns as $column) {
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
                    }
                    $table->timestamps();
                });
            }
        }
    }

    public function createBasicImports($producer)
    {
        $name = $producer->name." basic import de";
        if(!Import::where('name', $name)->count()) {
            Import::create([
                'producer_id'   =>      $producer->id,
                'name'          =>      $name,
                'language'      =>      'de',
                'status'        =>      'draft',
                'part_import_file'      =>     $producer->unique_id."_basic_import.php",
                'previous_import_id'    =>      null,
                'one_to_one'    =>      json_encode([]),
                'category_mapping'=>      json_encode([]),
                'price_mapping' =>      json_encode([]),
                'pdf_mapping' =>      json_encode([])
            ]);
        }
    }
}
