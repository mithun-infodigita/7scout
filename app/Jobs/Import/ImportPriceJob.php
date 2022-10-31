<?php

namespace App\Jobs\Import;

use App\Events\MergeRequest\MergeRequestCompletedEvent;
use App\Imports\PriceImport\MapImportPriceRules;
use App\Models\Column\Column;
use App\Models\PartIndex\PartIndex;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Translation\Dumper\YamlFileDumper;

class ImportPriceJob implements ShouldQueue

{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 3600;

    public $import;

    public function __construct($import)
    {
        $this->import = $import;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $mapImportPriceRules = new MapImportPriceRules();
        $mapImportPriceRules->mapData($this->import);
    }

}
