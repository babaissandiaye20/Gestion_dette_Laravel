<?php

namespace App\Console;

use App\Jobs\Archivage;
use App\Jobs\MongoArchivage;
use App\Jobs\SendDebtNotificationJob;
use Illuminate\Console\Scheduling\Schedule;
use App\Jobs\UploadUserPhotoToCloudinaryJob;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;


class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
     /*   $schedule->job( app(UploadUserPhotoToCloudinaryJob::class))->everyMinute(); */
     /*   $schedule->job(app(SendDebtNotificationJob::class))->everyMinute();   */
    /*     $schedule->job(new Archivage)->everyMinute(); */
        // $schedule->command('inspire')->hourly(); ArchiveSoldeJob
        $schedule->job(app(Archivage::class))->everyMinute();
   /* /* The line `->job(new MongoArchivage)->everyMinute();` is scheduling the
   `MongoArchivage` job to run every minute. This means that the `MongoArchivage` job will be
   executed once every minute according to the schedule defined in the Laravel application. */
     /*  $schedule->job(new MongoArchivage)->everyMinute();  */   
    }
    
    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
