<?php

namespace App\Console;

use App\Console\Commands\AdmavenStatistics;
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
        AdmavenStatistics::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
         $schedule->command('update-feed-stat Admaven')->dailyAt('23:00');
         $schedule->command('update-feed-stat Adjux')->dailyAt('23:00');
         $schedule->command('update-feed-stat Megapush')->dailyAt('23:00');
         $schedule->command('update-feed-stat Admashin')->dailyAt('23:00');
         $schedule->command('update-feed-stat AdsCompass')->dailyAt('23:00');
         $schedule->command('update-feed-stat AdsKeeper')->dailyAt('23:00');
         $schedule->command('update-feed-stat ZeroPark')->dailyAt('23:00');
         $schedule->command('update-feed-stat TrafficMedia')->dailyAt('23:00');
         $schedule->command('update-feed-stat Propeller')->dailyAt('23:00');
         $schedule->command('update-feed-stat PpcBuzz')->dailyAt('23:00');
         $schedule->command('update-feed-stat LizardTrack')->dailyAt('23:00');
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
