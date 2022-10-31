<?php


namespace App\Imports\PartImports;


use App\Models\Category\Category;
use App\Models\Group\Group;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Facades\Excel;


class XLSPartImportWithHeadingRow implements ToCollection, WithHeadingRow
{
    public $import;

    public function mapData($import)
    {
        $this->import = $import;
        $files = $import->getMedia();

        foreach ($files as $file) {
            if (file_exists($file->getPath())) {
                Excel::import($this, $file->getPath());
            }
        }
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row)
        {
            DB::table('import_parts_'.$this->import->id)->upsert(
                $this->getColumns($row, $this->import),
                config('columns.reference_id'),
                config('columns.to_update')
            );
        }
    }

    public function getColumns($part, $import) {
        $columns = [];
        foreach (config('columns.all') as $column) {
            $columns[$column] = $this->mapColumn($part, $column, $import->importRules, $import->language);
        }
        return $columns;
    }

    public function mapColumn($part, $column, $importRules, $language) {
        $val = null;
        $columnRules = $importRules->where('column', $column);
        foreach ($columnRules as $rule) {

            if($rule->rule_type=== 'fix_text') {
                $val = $rule->text_value;
            }

            if($rule->rule_type=== 'map') {
                $val = eval("return ".$rule->map_value_script.";");
            }

            if($rule->rule_type === 'category') {
                if ($rule->compare_type === 'contains') {
                    if (Str::contains(eval("return " . $rule->part_value_script . ";"), $rule->compare_value)) {
                        $val = Category::where('id', $rule->category_id)->first()->$language;
                    }
                }
            }

            if($rule->rule_type === 'group') {
                if ($rule->compare_type === 'contains') {
                    if (Str::contains(eval("return " . $rule->part_value_script . ";"), $rule->compare_value)) {
                        $val = Group::where('id', $rule->group_id)->first()->$language;
                    }
                }
            }

            if($rule->rule_type === 'full_record') {
                $val = json_encode($part);
            }

        }

        return $val;
    }




}
