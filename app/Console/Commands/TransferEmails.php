<?php

namespace App\Console\Commands;

use App\Classes\cPanelHelper;
use App\Classes\Helper;
use App\Facility;
use Illuminate\Console\Command;

class TransferEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'TransferEmails';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        foreach (Facility::where('active', 1)->get() as $facility) {
            foreach(['atm','datm','ta','ec','fe','wm'] as $position) {
                $destination = null; $email = $facility . "-" . $position . "@vatusa.net";
                if ($facility->{$position} == 0) {
                    if ($position == "atm") { $destination = "vatusa" . $facility->region . "@vatusa.net"; }
                    else { $destination = "$facility-sstf@vatusa.net"; }
                } else {
                    if(cPanelHelper::getType($email) == 1) {
                        $destination = cPanelHelper::getDest($email);
                    } else {
                        $destination = Helper::emailFromCID($facility->{$position});
                    }
                }

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, "https://api.vatusa.net/v2/email");
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    'Authorization' => 'Bearer ' . env('API_TOKEN')
                ]);
                curl_setopt($ch, CURLOPT_POSTFIELDS, ['email' => $email, 'destination' => $destination, 'static' => "false"]);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                $result = curl_exec($ch);
                if ($result == false) {
                    var_dump("Error with $email -> $destination: " . curl_error($ch));
                }
                curl_close($ch);
            }
        }
    }
}
