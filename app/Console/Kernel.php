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
        'App\Console\Commands\ExamReassign',
        'App\Console\Commands\ULSTokens',
        'App\Console\Commands\TattlerStaffVisit',
        'App\Console\Commands\TattlerTransfers',
        'App\Console\Commands\GroupCheck',
        'App\Console\Commands\UpdateVATSIM',
        'App\Console\Commands\RoleSync',
        'App\Console\Commands\TransferEmails',
        'App\Console\Commands\ExpireNotices',
        'App\Console\Commands\CERTCorrect'
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
        $schedule->command('CERTSync')->twiceDaily(11, 23);
        $schedule->command('CERTSync', ['--all'])->weekly()->mondays()->at("00:00");
        $schedule->command('ULSTokens')->everyMinute();
        $schedule->command('UpdateVATSIM')->everyMinute();
        $schedule->command('ExamReassign')->hourly();
        $schedule->command('TattlerTransfers')->cron('15 0 * * *');
        $schedule->command("TattlerStaffVisit")->weekly()->sundays()->at("23:00");
        $schedule->command('rolesync')->cron('45 * * * *');
        $schedule->command('ntos:expire')->weekly();
    }

}
