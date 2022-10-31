<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [

    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        if($_ENV['APP_ENV'] === 'production') {
            $schedule->command('backup:run')->daily()->at('10:15');
            $schedule->command('backup:run')->daily()->at('12:15');
            $schedule->command('backup:run')->daily()->at('15:15');
            $schedule->command('backup:clean')->daily()->at('18:15');
            $schedule->command('backup:run')->daily()->at('19:15');
            $schedule->command('stockUpdate:nachreiner')->hourlyAt(55);
            $schedule->command('stockUpdate:diebold')->everyFifteenMinutes();
            $schedule->command('stockUpdate:amf')->dailyAt('06:45');
            $schedule->command('stockUpdate:amf')->dailyAt('07:45');
            $schedule->command('stockUpdate:amf')->dailyAt('08:45');
        }
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
