<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Facility;
use App\Role;

class RoleSync extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rolesync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync facility roles to roles table';

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
        // For dev cycle of new site...
        \DB::table('roles')->where('role', 'ATM')->delete();
        \DB::table('roles')->where('role', 'DATM')->delete();
        \DB::table('roles')->where('role', 'TA')->delete();
        \DB::table('roles')->where('role', 'FE')->delete();
        \DB::table('roles')->where('role', 'EC')->delete();
        \DB::table('roles')->where('role', 'WM')->delete();
        foreach(Facility::where('active', 1)->get() as $facility) {
            if ($facility->atm != 0) {
                $role = new Role();
                $role->cid = $facility->atm;
                $role->facility = $facility->id;
                $role->role = "ATM";
                $role->save();
            }
            if ($facility->datm != 0) {
                $role = new Role();
                $role->cid = $facility->datm;
                $role->facility = $facility->id;
                $role->role = "DATM";
                $role->save();
            }
            if ($facility->ta != 0) {
                $role = new Role();
                $role->cid = $facility->ta;
                $role->facility = $facility->id;
                $role->role = "TA";
                $role->save();
            }
            if ($facility->fe != 0) {
                $role = new Role();
                $role->cid = $facility->fe;
                $role->facility = $facility->id;
                $role->role = "FE";
                $role->save();
            }
            if ($facility->ec != 0) {
                $role = new Role();
                $role->cid = $facility->ec;
                $role->facility = $facility->id;
                $role->role = "EC";
                $role->save();
            }
            if ($facility->wm != 0) {
                $role = new Role();
                $role->cid = $facility->wm;
                $role->facility = $facility->id;
                $role->role = "WM";
                $role->save();
            }
        }
    }
}
