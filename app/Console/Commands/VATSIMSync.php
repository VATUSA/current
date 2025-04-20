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

        $page = 0;
        while (true) {
            echo "Fetching page {$page} \n";
            $data = VATSIMApi2Helper::fetchOrgMemberPage($page);
            if ($data) {
                if (count($data['items']) == 0) break;
                foreach ($data['items'] as $item) {
                    echo "Syncing {$item['id']} from VATSIM Org Roster \n";
                    VATSIMApi2Helper::processMemberData($item);
                }
            }
            $page++;
        }

        $unsynced_division_controllers = User::where('flag_homecontroller', 1)
            ->where('rating', '>=', 0)
            ->where(function ($query) {
                $query->where('last_cert_sync', '<=', Carbon::now()->subDay()->toDateTimeString());
                $query->orWhereNull('last_cert_sync');
            })
            ->get();

        foreach ($unsynced_division_controllers as $controller) {
            echo "USD - Syncing {$controller->cid} - Last Sync: {$controller->last_cert_sync}\n";
            VATSIMApi2Helper::syncCID($controller->cid);
        }

        $external_visit_eligible = User::where('facility', 'ZZN')
            ->where('rating', '>', 1)
            ->where(function ($query) {
                $query->where('last_cert_sync', '<=', Carbon::now()->subDay()->toDateTimeString());
                $query->orWhereNull('last_cert_sync');
            })
            ->get();
        foreach ($external_visit_eligible as $controller) {
            echo "EVE - Syncing {$controller->cid} - Last Sync: {$controller->last_cert_sync}\n";
            VATSIMApi2Helper::syncCID($controller->cid);
        }
        return 0;
    }
}