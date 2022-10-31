<?php

namespace App\Imports\DutyTax;

use App\Models\DutyTax\DutyTax;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithStartRow;

class CHDutyTaxesImport implements ToModel, WithStartRow, WithChunkReading, ShouldQueue
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
            return new DutyTax([
                'import_country' => 'CH',
                'tariff_number' => $row[0],
                'export_country_number' => $row[10],
                'export_country' => $row[14],
                'tax' => $row[15]
            ]);

//        return DutyTax::create([
//            'import_country' => 'CH', 'tariff_number' => $row[0],'export_country_number' => $row[10],
//        ],
//            [
//                'export_country' => $row[14],
//                'tax' => $row[15]
//            ]);
    }

    public function startRow(): int
    {
        return 3;
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}

