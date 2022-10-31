<?php

namespace App\Models\CustomsSetting;

use Illuminate\Database\Eloquent\Model;
use Spatie\EloquentSortable\SortableTrait;

class CustomsSetting extends Model
{
    use CustomsSettingAttribute,
        CustomsSettingMethod,
        CustomsSettingRelationship,
        CustomsSettingScope,
        SortableTrait;

    protected $fillable = [
        'customs_tariff_number_eu',
        'customs_tariff_number_ch',
        'import_fees_with_preferential_origin_of_goods_eu',
        'import_fees_with_preferential_origin_of_goods_ch',
        'import_fees_without_preferential_origin_of_goods_eu',
        'import_fees_without_preferential_origin_of_goods_ch',
        'tax_unit_eu',
        'tax_unit_ch',
        'tax_by_value_eu',
        'tax_by_value_ch'
    ];

    protected $hidden = [

    ];

    protected $appends = [

    ];

    protected $with = [

    ];

    protected $casts = [

    ];



}
