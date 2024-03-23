<?php namespace App\Console;

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
        'App\Console\Commands\CERTSync',
        'App\Console\Commands\TattlerStaffVisit',
        'App\Console\Commands\TattlerTransfers',
        'App\Console\Commands\GroupCheck',
        'App\Console\Commands\UpdateVATSIM',
        'App\Console\Commands\RoleSync',
        'App\Console\Commands\ExpireNotices',
        'App\Console\Commands\MoodleSync',
        'App\Console\Commands\VATSIMSync',
        'App\Console\Commands\CachePromotionEligibility'
    ];

    /**
     * Define the application's command schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     *
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
//        $schedule->command('CERTSync')->twiceDaily(11, 23);
        $schedule->command('UpdateVATSIM')->everyMinute();
        $schedule->command('TattlerTransfers')->cron('15 0 * * *');
        // $schedule->command("TattlerStaffVisit")->weekly()->sundays()->at("23:00");
//        $schedule->command('rolesync')->cron('45 * * * *');
        $schedule->command('ntos:expire')->weekly();
        $schedule->command('promos:cacheeligible')->dailyAt('05:00');
        $schedule->command('vatsim:sync')->hourly();
    }

}
