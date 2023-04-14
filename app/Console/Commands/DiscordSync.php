<?php

namespace App\Console\Commands;

use App\Classes\DiscordHelper;
use App\Models\User;
use Illuminate\Console\Command;
use App\Models\Facility;
use App\Models\Role;

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
        User::chunk(1000, function ($users) {
            foreach ($users as $user) {
                if ($user->flag_homecontroller = 1 || count($user->visits()) > 0) {
                    print("Assigning CID $user->cid");
                    $result = DiscordHelper::assignRoles($user->cid);
                    print($result . "/n/n");
                }
            }
        });
        return 0;
    }
}
