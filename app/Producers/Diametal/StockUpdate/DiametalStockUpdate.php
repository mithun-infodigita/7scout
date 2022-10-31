<?php

namespace App\Producers\Diametal\StockUpdate;

use App\Models\Indices\PartIndexDe;
use App\Models\Indices\PartIndexEn;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Facades\Excel;

class DiametalStockUpdate implements ToCollection, WithHeadingRow
{
    public function update()
    {
        $files = Storage::disk('diametalStockData')->allFiles();

        foreach ($files as $file) {

            $fileToCopy = Storage::disk('diametalStockData')->get($file);

            Storage::disk('temporaryFiles')->put($file, $fileToCopy);

            Excel::import($this, storage_path('temporaryFiles/'.$file));

            Storage::disk('diametalStockData')->delete($file);

            Storage::disk('temporaryFiles')->delete($file);
        }
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $this->updateStock($row);
        }
    }

    public function updateStock($row)
    {
        $items = explode('|', $row['artikelnrlagerbestandme']);

        $partDe = PartIndexDe::where('part_id', 'diametal_'.$items[0])->first();

        if($partDe) {
            $partDe->update([
                'stock'=> json_encode([
                    2 => trim($items[1])
                ])
            ]);
        }

        $partEn = PartIndexEn::where('part_id', 'diametal_'.$items[0])->first();
        if($partEn) {
            $partEn->update([
                'stock'=> json_encode([
                    2 => trim(trim($items[1]))
                ])
            ]);
        }
    }
}
