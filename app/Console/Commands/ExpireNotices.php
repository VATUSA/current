<?php

namespace App\Console\Commands;

use App\TMUNotice;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class ExpireNotices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ntos:expire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete expired NTOS Notices';

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
        $notices = TMUNotice::where('expire_date', '<=', Carbon::now())->get();
        foreach ($notices as $notice) {
            $notice->delete();
        }
    }
}
