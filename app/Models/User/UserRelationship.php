<?php

namespace App\Models\User;

use App\Models\Comment\Comment;
use App\Models\Company\Company;
use App\Models\Controlling\DashboardFilter\DashboardFilter;
use App\Models\Issue\Issue;
use App\Models\Note\Note;
use App\Models\Project\Project;
use App\Models\Project\ProjectPermission\ProjectPermission;
use App\Models\Todo\Todo;
use App\Models\GlobalFeature\TableFilter\TableFilter;

trait UserRelationship
{
    public function tableFilters()
    {
        return $this->hasMany(TableFilter::class);
    }
}
