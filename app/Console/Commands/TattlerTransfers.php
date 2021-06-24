<?php namespace App\Console\Commands;

use App\Classes\EmailHelper;
use App\Classes\Helper;
use Illuminate\Console\Command;
use App\Transfers;
use App\User;
use App\Facility;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class TattlerTransfers extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'TattlerTransfers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends notification of transfers that are older than 5 days';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $transfers = Transfers::whereRaw("DATE_ADD(created_at, INTERVAL " . config('tattlers.transfers.maxdays',
                7) . " DAY) < NOW()")->where('status', 0)->get();
        if ($transfers) {
            foreach ($transfers as $transfer) {
                $user = User::find($transfer->cid);
                $rating = Helper::ratingShortFromInt($user->rating);
                $to = Facility::find($transfer->to);
                EmailHelper::sendEmail(
                    [
                        $transfer->to . "-atm@vatusa.net",
                        $transfer->to . "-datm@vatusa.net",
                        "vatusa" . $to->region . "@vatusa.net",
                        "vatusa1@vatusa.net",
                        "vatusa2@vatusa.net",
                        //     "vatusa6@vatusa.net"
                    ],
                    "Tattler: Transfer Pending",
                    "emails.tattlers.transferpending",
                    [
                        'name'    => $user->fullname(),
                        'cid'     => $user->cid,
                        'rating'  => $rating,
                        'gaining' => $transfer->to,
                        'losing'  => $transfer->from,
                        'reason'  => $transfer->reason,
                        'days'    => config('tattlers.transfers.maxdays', 7),
                        'date'    => $transfer->created_at
                    ]
                );
            }
        }

        return 0;
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
        ];
    }
}
