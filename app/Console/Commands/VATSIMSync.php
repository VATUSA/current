<?php

namespace App\Console\Commands;

use App\Classes\VATSIMApi2Helper;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class VATSIMSync extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vatsim:sync 
                            {user? : CID of a single user to sync}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync VATSIM Certificate';



    /**
     * Execute the console command.
     *
     * @return mixed
     * @throws \Exception
     */
    public function handle() {
        if ($this->argument('user')) {
            $user = User::find($this->argument('user'));
            if (!$user) {
                $this->error("Invalid CID");

                return 0;
            }

            VATSIMApi2Helper::syncCID($user->cid);

            return 0;
        }
        $users = User::limit(250)
            ->where('rating', '>=', 0)
            ->where(function ($query) {
                $query->where('last_cert_sync', '<=', Carbon::now()->subHours(1)->toDateTimeString());
                $query->orWhereNull('last_cert_sync');
            })->orderBy('last_cert_sync', 'asc')
            ->get();
        foreach ($users as $user) {
            echo "Syncing User {$user->cid} - Last Sync: {$user->last_cert_sync}\n";
            VATSIMApi2Helper::syncCID($user->cid);
        }
        return 0;
    }
}