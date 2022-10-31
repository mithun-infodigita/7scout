<?php

namespace App\Models\ImportPriceRule;

use App\Models\Import\Import;

trait ImportPriceRuleRelationship
{
    public function import()
    {
        return $this->belongsTo(Import::class);
    }
}
