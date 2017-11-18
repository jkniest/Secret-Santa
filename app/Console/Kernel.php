<?php

namespace App\Console;

use App\Jobs\Draw;
use App\Jobs\EndParticipation;
use App\Jobs\GivePresents;
use App\Jobs\HappyNewYear;
use App\Jobs\Start;
use App\Jobs\Stop;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

/**
 * The console kernel.
 *
 * @category Core
 * @package  SecretSanta
 * @author   Jordan Kniest <contact@jkniest.de>
 * @license  MIT <opensource.org/licenses/MIT>
 * @link     https://jkniest.de
 */
class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule The scheduler
     *
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->job(Start::class)->everyMinute();
        $schedule->job(EndParticipation::class)->everyMinute();
        $schedule->job(Draw::class)->everyMinute();
        $schedule->job(GivePresents::class)->everyMinute();
        $schedule->job(HappyNewYear::class)->everyMinute();
        $schedule->job(Stop::class)->everyMinute();
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
