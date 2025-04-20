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
        // Helper function to create a 'before' hook closure with the command name
        $createBeforeHook = function (string $commandName) {
            return function () use ($commandName) {
                // Use logger() or Log::info() etc.
                logger("Starting scheduled task: {$commandName}");
            };
        };

        // Helper function to create an 'after' hook closure with the command name
        $createAfterHook = function (string $commandName) {
            return function () use ($commandName) {
                logger("Finished scheduled task: {$commandName}");
            };
        };


        $commandName = 'UpdateVATSIM';
        $schedule->command($commandName)
            ->everyMinute()
            ->onOneServer()
            ->before($createBeforeHook($commandName))
            ->after($createAfterHook($commandName));

        $commandName = 'TattlerTransfers';
        $schedule->command($commandName)
            ->cron('15 0 * * *')
            ->onOneServer()
            ->withoutOverlapping()
            ->before($createBeforeHook($commandName))
            ->after($createAfterHook($commandName));

        $commandName = 'ntos:expire';
        $schedule->command($commandName)
            ->weekly()
            ->onOneServer()
            ->withoutOverlapping()
            ->before($createBeforeHook($commandName))
            ->after($createAfterHook($commandName));

        $commandName = 'promos:cacheeligible';
        $schedule->command($commandName)
            ->dailyAt('05:00')
            ->onOneServer()
            ->withoutOverlapping()
            ->before($createBeforeHook($commandName))
            ->after($createAfterHook($commandName));

        $commandName = 'vatsim:sync';
        $schedule->command($commandName)
            ->hourly()
            ->onOneServer()
            ->withoutOverlapping()
            ->before($createBeforeHook($commandName))
            ->after($createAfterHook($commandName));


        // Apply hooks individually to each command
        // $schedule->command('CERTSync')->twiceDaily(11, 23)->before($beforeHook)->after($afterHook);

        // $schedule->command("TattlerStaffVisit")->weekly()->sundays()->at("23:00")->before($beforeHook)->after($afterHook);
        // $schedule->command('rolesync')->cron('45 * * * *')->before($beforeHook)->after($afterHook);

    }
}
