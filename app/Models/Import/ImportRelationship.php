<?php

namespace App\Models\Import;

use App\Models\AdditionalFileDataRule\AdditionalFileDataRule;
use App\Models\ImportPriceRule\ImportPriceRule;
use App\Models\ImportRule\ImportRule;
use App\Models\Producer\Producer;

trait ImportRelationship
{
    public function producer()
    {
        return $this->belongsTo(Producer::class);
    }

    public function importRules()
    {
        return $this->hasMany(ImportRule::class);
    }

    public function importPriceRules()
    {
        return $this->hasMany(ImportPriceRule::class);
    }


}
