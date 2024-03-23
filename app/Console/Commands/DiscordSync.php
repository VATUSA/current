<?php

namespace App\Console\Commands;

use App\Helpers\DiscordHelper;
use App\Models\User;
use Illuminate\Console\Command;

class DiscordSync extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'discord:rolesync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync discord roles from roles table';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        User::whereNotNull('discord_id')->chunk(1000, function ($users) {
            print("\n Next Batch \n");
            foreach ($users as $user) {
                if ($user->flag_homecontroller = 1 || count($user->visits()) > 0) {
                    print("Assigning CID $user->cid \n");
                    try {
                        $result = DiscordHelper::assignRoles($user->cid);
                        print($result . "\n\n");
                    } catch (\Exception $exception) {
                        print($exception->getMessage() . "\n\n");
                    }
                }
            }
        });
        return 0;
    }
}
