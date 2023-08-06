<?php

namespace App\Classes;

use App\Models\Actions;
use App\Models\Transfers;
use App\Models\User;

class TransferHelper
{
    public static function requestTransfer(User $user, string $facility, string $reason) {
        $tr = new Transfers();
        $tr->cid = $user->cid;
        $tr->to = $facility;
        $tr->from = $user->facility;
        $tr->reason = $reason;
        $tr->save();

        $log = new Actions;
        $log->from = 0;
        $log->to = Auth::user()->cid;
        $log->log = "Requested transfer from " . $tr->from . " to " . $tr->to . ": " . $tr->reason;
        $log->save();



        EmailHelper::sendEmail([
            $tr->from . "-atm@vatusa.net",
            $tr->from . "-datm@vatusa.net",
            $tr->to . "-atm@vatusa.net",
            $tr->to . "-datm@vatusa.net",
            "vatusa2@vatusa.net",
        ], "Transfer Pending", "emails.transfers.internalpending", [
            'fname' => $user->fname,
            'lname' => $user->lname,
            'cid' => $tr->cid,
            'facility' => $facility,
            'reason' => $_POST['reason']
        ]);
    }

    public static function forceTransfer(User $user, string $facility, string $reason) {
        $tr = new Transfers();
        $tr->cid = $user->cid;
        $tr->to = $facility;
        $tr->from = $user->facility;
        $tr->reason = $reason;
        $tr->status = 1;
        $tr->actionby = 0;
        $tr->save();

        $log = new Actions;
        $log->from = 0;
        $log->to = Auth::user()->cid;
        $log->log = "Forced transfer from " . $tr->from . " to " . $tr->to . ": " . $tr->reason;
        $log->save();

        $user->addToFacility($facility);
    }
}