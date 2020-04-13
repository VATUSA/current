<?php

namespace App\Console\Commands;

use App\Actions;
use App\Classes\EmailHelper;
use App\Classes\Helper;
use App\Transfers;
use App\User;
use GuzzleHttp\Client;
use GuzzleHttp\Client as Guzzle;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Console\Command;
use Illuminate\Notifications\Action;
use Illuminate\Support\Carbon;

class CERTCorrect extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'CERTCorrect';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix cert';

    private $guzzle;

    public function __construct()
    {
        parent::__construct();

        $this->guzzle = new Guzzle();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        foreach (Actions::where('id', '>=', 714248)->where('log', 'LIKE', '%division')->get() as $action) {
            $retries = 0;
            $error = false;
            while (true) {
                if ($retries > 3) {
                    $this->error("3 consecutive errors occurred. Aborting.");
                    exit(2);
                }
                try {
                    $data = $this->guzzle->get("https://cert.vatsim.net/vatsimnet/idstatus.php?cid={$action->to}");
                } catch (RequestException $e) {
                    if ($e->hasResponse()) {
                        if ($e->getResponse()->getStatusCode() == 404) {
                            continue 2;
                        } else {
                            $error = true;
                            // $this->error(\GuzzleHttp\Psr7\str($e->getRequest()) . "\n" . \GuzzleHttp\Psr7\str($e->getResponse()));
                            // $this->line("Error after $i");
                        }
                    } else {
                        $error = true;
                        // $this->error(\GuzzleHttp\Psr7\str($e->getRequest()) . "\n" . \GuzzleHttp\Psr7\str($e->getResponse()));
                        // $this->line("Error after $i");
                    }
                }

                if ($error) {
                    $retries++;
                    $error = false;
                    sleep(2);
                } else {
                    break;
                }
            }
            // $this->line($i);
            $xml = simplexml_load_string($data->getBody());
            $xmlUser = $xml->user[0];

            $division = (string)$xmlUser->division ?? null;
            $rating = (string)$xmlUser->rating ?? null;

            if (is_null($division) || is_null($rating)) {
                $this->log[] = "XML Error. Skipping.";
                continue;
            }

            if ($rating === "Inactive") {
                $oldmessage = $action->log;
                $action->log = str_replace("Left division", "Inactive", $oldmessage);
                $action->save();
                $transfer = Transfers::where('cid', $action->to)->where('reason', 'Left division')->orderBy('id', 'DESC')->first();
                $transfer->reason = "Inactive";
                $transfer->actiontext = "Inactive";
                $transfer->save();
            }
        }
    }
}
