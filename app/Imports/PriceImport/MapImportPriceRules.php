<?php


namespace App\Imports\PriceImport;


use App\Models\Column\Column;


use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Facades\Excel;

class MapImportPriceRules implements ToCollection, WithHeadingRow
{
    public $import;

    public function mapData($import)
    {
        Log::info('test');

        $this->import = $import;

        $files = $import->getMedia('priceFiles');

        foreach ($files as $file) {
            if (file_exists($file->getPath())) {
                Excel::import($this, $file->getPath());
            }
        }
    }


    public function collection(Collection $rows)
    {
        if($this->import->importPriceRules->count() > 1) {
            foreach ($rows as $row) {
                $prices = [];
                foreach ($this->import->importPriceRules as $importPriceRule) {
                    $sourceReferenceColumn = Column::where('id', $importPriceRule->source_reference_column_id)->first()->name;

                    if ($importPriceRule->reference_compare_type === 'contains') {
                        if (DB::table($this->import->producer->unique_id . '_parts_' . $this->import->language)->where($sourceReferenceColumn, 'like', '%' . eval("return " . $importPriceRule->map_reference_script . ";") . '%')->count()) {
                            $prices[$importPriceRule->country] = [
                                'currency' => $importPriceRule->currency,
                                'value' => eval("return " . $importPriceRule->map_value_script . ";")
                            ];
                        }

                    }
                }
                DB::table($this->import->producer->unique_id . '_parts_' . $this->import->language)->where($sourceReferenceColumn, 'like', '%' . eval("return " . $importPriceRule->map_reference_script . ";") . '%')
                    ->update(
                        [
                            'price' => json_encode($prices)
                        ]
                    );
            }
        }
        else {
            foreach ($rows as $row) {
                foreach ($this->import->importPriceRules as $importPriceRule) {
                    $sourceReferenceColumn = Column::where('id', $importPriceRule->source_reference_column_id)->first()->name;

                    if ($importPriceRule->reference_compare_type === 'contains') {
                        if (DB::table($this->import->producer->unique_id . '_parts_' . $this->import->language)->where($sourceReferenceColumn, 'like', '%' . eval("return " . $importPriceRule->map_reference_script . ";") . '%')->count()) {
                            DB::table($this->import->producer->unique_id . '_parts_' . $this->import->language)->where($sourceReferenceColumn, 'like', '%' . eval("return " . $importPriceRule->map_reference_script . ";") . '%')
                                ->update(
                                    [
                                        'price' => json_encode([
                                            'currency' => $importPriceRule->currency,
                                            'value' => eval("return " . $importPriceRule->map_value_script . ";")
                                        ])
                                    ]
                                );
                        }

                    }
                }
            }
        }



    }
}
