<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();

        // $schedule->call(function () {
        //     // Call the MailBuyerWiseTnaSummary method
        //     (new \App\Http\Controllers\TNAController)->MailBuyerWiseTnaSummary();
        // })->dailyAt('12:27');


        //// Call the MailBuyerWiseTnaSummary method in every minute
        $schedule->call(function () {
            // Call the MailBuyerWiseTnaSummary method
            (new \App\Http\Controllers\TNAController)->MailBuyerWiseTnaSummary();
        })->dailyAt('9:23');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
