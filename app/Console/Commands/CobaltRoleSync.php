<?php

namespace App\Console\Commands;

use App\Cobalt\CobaltAPIHelper;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CobaltRoleSync extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cobalt:sync_roles {cid? : CID of a user to sync}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync roles to Cobalt';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    private function formatRoles($roles) {
        $cidRoles = [];
        foreach ($roles as $role) {
            if (!array_key_exists($role->cid, $cidRoles)) {
                $cidRoles[$role->cid] = [];
            }
            $cidRoles[$role->cid][] = ["role" => $role->role, "facility" => $role->facility];
        }
        $output = [];
        foreach ($cidRoles as $cid => $roles) {
            $output[] = [
                'cid' => $cid,
                'roles' => $roles,
            ];
        }
        return ["requests" =>$output];
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if ($this->argument('cid')) {
            $cid = $this->argument('cid');
            $roles = DB::select("SELECT cid, role, facility FROM roles WHERE cid = {$cid}");
            $request = $this->formatRoles($roles);
            CobaltAPIHelper::postSyncRolesBulk($request);
            return 0;
        }

        $roles = DB::select("SELECT cid, role, facility FROM roles ORDER BY cid");
        $request = $this->formatRoles($roles);
        CobaltAPIHelper::postSyncRolesBulk($request);
        return 0;
    }
}
