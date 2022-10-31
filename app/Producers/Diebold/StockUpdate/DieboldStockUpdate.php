<?php

namespace App\Producers\Diebold\StockUpdate;

use App\Models\Indices\PartIndexDe;
use App\Models\Indices\PartIndexEn;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Facades\Excel;

class DieboldStockUpdate implements ToCollection
{
    public function update()
    {
        $files = Storage::disk('diebold')->allFiles();

        foreach ($files as $file) {

            $fileToCopy = Storage::disk('diebold')->get($file);

            Storage::disk('temporaryFiles')->put($file, $fileToCopy);

            Excel::import($this, storage_path('temporaryFiles/'.$file));

            Storage::disk('diebold')->delete($file);

            Storage::disk('temporaryFiles')->delete($file);
        }
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $this->updateStock($row);
        }
    }

    public function updateStock($row) {
        $items = explode('|', $row[0]);

        $partDe = PartIndexDe::where('part_id', 'diebold_'.$items[2])->first();

        if($partDe) {
            $partDe->update([
                'stock'=> json_encode([
                    4 => trim($items[3])
                ])
            ]);
        }

        $partEn = PartIndexEn::where('part_id', 'diebold_'.$items[2])->first();

        if($partEn) {
            $partEn->update([
                'stock'=> json_encode([
                    4 => trim($items[3])
                ])
            ]);
        }
    }
}
