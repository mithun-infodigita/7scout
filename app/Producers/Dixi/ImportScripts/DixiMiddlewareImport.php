<?php

namespace App\Producers\Dixi\ImportScripts;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Facades\Excel;

class DixiMiddlewareImport implements ToCollection, WithHeadingRow
{
    public function basicImport($import)
    {
        $filePath = $import->getMedia('additionalFiles')->where('name', 'dixi_middleware_formatted')->first()->getPath();

        $import = Excel::toArray($this, $filePath);

        $middlewareItems = [];

        foreach ($import[0] as $item) {
            if (array_key_exists('gco_good_category', $item)) {
                $middlewareItems[$item['gco_good_category']] = $item;
            }
        }

        $middlewareCollection = collect($middlewareItems);

        $items = [];
        foreach ($middlewareCollection as $key => $value) {
            $items[$key] = collect($value)->filter(function ($value, $key) {
                return $value != null;
            });
        }

        return $items;


    }

    public function collection(Collection $rows)
    {

    }
}
