<?php

namespace App\Producers\Amf\StockUpdate;

use App\Models\Indices\PartIndexDe;
use App\Models\Indices\PartIndexEn;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Facades\Excel;

class AmfStockUpdate implements ToCollection, WithHeadingRow
{
    public function update()
    {
        $files = Storage::disk('amf')->allFiles();

        foreach ($files as $file) {

            $fileToCopy = Storage::disk('amf')->get($file);

            Storage::disk('temporaryFiles')->put($file, $fileToCopy);

            Excel::import($this, storage_path('temporaryFiles/'.$file));

            Storage::disk('amf')->delete($file);

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
        $items = explode(';', $row['supplier_aidquantity']);

        $partDe = PartIndexDe::where('part_id', 'amf_'.$items[0])->first();

        if($partDe) {
            $partDe->update([
                'stock'=> json_encode([
                    7 => trim($items[1])
                ])
            ]);
        }

        $partEn = PartIndexEn::where('part_id', 'amf_'.$items[0])->first();
        if($partEn) {
            $partEn->update([
                'stock'=> json_encode([
                    7 => trim(trim($items[1]))
                ])
            ]);
        }
    }
}
