<?php

namespace App\Models\ImportRule;

use App\Models\Import\Import;

trait ImportRuleRelationship
{
    public function import()
    {
        return $this->belongsTo(Import::class);
    }
}
