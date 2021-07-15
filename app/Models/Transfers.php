<?php
namespace App\Models;

use App\Classes\Helper;
use Illuminate\Database\Eloquent\Model;
use App\Classes\EmailHelper;

class Transfers
    extends Model
{
    protected $table = 'transfers';

    public function user()
    {
        return $this->hasOne(User::class, 'cid', 'cid')->first();
    }

    public function to()
    {
        return $this->hasOne(Facility::class, 'id', 'to')->first();
    }

    public function from()
    {
        return $this->hasOne(Facility::class, 'id', 'from')->first();
    }

    public function accept($by)
    {
        $this->status = 1;
        $this->actionby = $by;
        $this->save();

        $user = User::where('cid',$this->cid)->first();
        $user->addToFacility($this->to);
        EmailHelper::sendEmail(
            [
                $this->to . "-atm@vatusa.net",
                $this->to . "-datm@vatusa.net",
                "vatusa" . $this->to()->region . "@vatusa.net",
                $this->from . "-atm@vatusa.net",
                $this->from . "-datm@vatusa.net",
                "vatusa" . $this->from()->region . "@vatusa.net",
            ],
            "Transfer accepted",
            "emails.transfers.accepted",
            [
                'fname' => $this->user()->fname,
                'lname' => $this->user()->lname,
                'cid' => $this->user()->cid,
                'to' => $this->to,
                'from' => $this->from,
            ]
        );

        $by = User::where('cid', $by)->first();

        $log = new Actions();
        $log->from = 0;
        $log->to = $this->cid;
        $log->log = "Transfer request to " . $this->to . " accepted by " . $by->fname . " " . $by->lname . " (" . $by->cid . ")";
        $log->save();
    }

    public function reject($by, $msg)
    {
        $this->status = 2;
        $this->actiontext = $msg;
        $this->actionby = $by;
        $this->save();

        EmailHelper::sendEmail(
            [
                $this->user()->email,
                $this->to . "-atm@vatusa.net",
                $this->to . "-datm@vatusa.net",
                "vatusa" . $this->to()->region . "@vatusa.net",
                $this->from . "-atm@vatusa.net",
                $this->from . "-datm@vatusa.net",
                "vatusa" . $this->from()->region . "@vatusa.net"
            ],
            "Transfer request rejected",
            "emails.transfers.rejected",
            [
                'fname' => $this->user()->fname,
                'lname' => $this->user()->lname,
                'cid' => $this->cid,
                'facname' => $this->to()->name,
                'facid' => $this->to()->id,
                'region' => $this->to()->region,
                'by' => Helper::nameFromCID($by),
                'msg' => $msg
            ]
        );

        $by = User::where('cid', $by)->first();

        $log = new Actions();
        $log->from = 0;
        $log->to = $this->cid;
        $log->log = "Transfer request to " . $this->to . " rejected by " . $by->fname . " " . $by->lname . " (" . $by->cid . "): " . $msg;
        $log->save();
    }
}
