<?php

namespace App\Console;

use App\Jobs\Archivage;
use App\Jobs\Archivagerecup;
use App\Jobs\MongoArchivage;
use App\Jobs\ArchiverDebtsJob;
use App\Jobs\SendDebtNotificationJob;
use Illuminate\Console\Scheduling\Schedule;
use App\Jobs\UploadUserPhotoToCloudinaryJob;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Jobs\SendDebtReminderJob;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.

     */protected $commands = [
           \App\Console\Commands\VideCache::class,
       ];

    protected function schedule(Schedule $schedule): void
    {
     /*   $schedule->job( app(UploadUserPhotoToCloudinaryJob::class))->everyMinute(); */
      //$schedule->job(app(SendDebtNotificationJob::class))->everyMinute();
     //  $schedule->job(new Archivage)->everyMinute();
        // $schedule->command('inspire')->hourly(); ArchiveSoldeJob
      //  $schedule->job(app(Archivage::class))->everyMinute();

     /*  $schedule->job(new MongoArchivage)->everyMinute();  */
     //$schedule->job(new ArchiverDebtsJob())->everyMinute();
         $schedule->job(new SendDebtReminderJob())->everyMinute();

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
