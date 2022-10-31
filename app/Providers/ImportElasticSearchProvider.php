<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Scout\Console\ImportCommand;

class ImportElasticSearchProvider extends ServiceProvider
{

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        $this->commands([
            ImportCommand::class,
        ]);
    }
}
