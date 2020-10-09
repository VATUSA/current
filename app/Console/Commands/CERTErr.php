<?php

namespace App\Console\Commands;

use App\Actions;
use App\Transfers;
use App\User;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class CERTErr extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'CERTErr';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix cert';

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
        $cids = Actions::where('log', 'like', '%division')->where('log', 'like',
            'Removed%')->where(\DB::raw('DATE(created_at)'),
            '2020-09-21')->pluck('to');
        $corrected = [];
        $i = 0;
        foreach ($cids as $cid) {
            $data = (new Client())->get('https://cert.vatsim.net/vatsimnet/idstatus.php?cid=' . $cid);
            $div = simplexml_load_string($data->getBody())->user[0]->division;
            if ($div == "United States") {
                $user = User::find($cid);
                $transfer = Transfers::where('cid', $cid)->where('actiontext', "Left division")
                    ->where(\DB::raw("DATE_ADD(created_at, INTERVAL 90 day)"), '>=', Carbon::now())
                    ->orderBy('created_at', 'DESC')->first();

                $user->facility = $transfer->from;
                $user->flag_homecontroller = 1;
                $user->save();
                $transfer->delete();
                $corrected[] = $cid;
                $i++;
            }
        }
        foreach ($corrected as $cid) {
            Actions::where('log', 'like', '%division')->where('log', 'like',
                'Removed%')->where(\DB::raw('DATE(created_at)'),
                '2020-09-21')->where('to', $cid)->first()->delete();
        }
        $this->info("Corrected $i");
    }
}
