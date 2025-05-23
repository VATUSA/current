<?php namespace App\Models;

use App\Classes\ExamHelper;
use App\Classes\RoleHelper;
use App\Classes\SMFHelper;
use App\Classes\VATUSAMoodle;
use Carbon\Carbon;
use App\Classes\EmailHelper;
use App\Classes\Helper;
use App\Classes\VATSIMApi2Helper;
use Exception;
use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class User extends Model implements AuthenticatableContract, CanResetPasswordContract
{
    use Authenticatable, CanResetPassword;

    protected $table = "controllers";
    public $primaryKey = "cid";
    public $incrementing = false;
    public $timestamps = ["created_at", "updated_at", "prefname_date"];
    protected $hidden = [
        "password",
        "remember_token",
        "cert_update",
        "access_token",
        "refresh_token",
        "token_expires",
        "prefname",
        "prefname_date"
    ];

    public function getDates()
    {
        return ["created_at", "updated_at", "lastactivity"];
    }

    public function fullname($lf = false)
    {
        return $lf ? $this->lname . ", " . $this->fname : $this->fname . " " . $this->lname;
    }

    public function facility()
    {
        return $this->belongsTo(Facility::class, 'facility')->first();
    }

    public function facilityObj()
    {
        return $this->belongsTo(Facility::class, 'facility');
    }

    public function urating()
    {
        return $this->hasOne(Rating::class, 'id', 'rating');
    }

    public function trainingRecords()
    {
        return $this->hasMany(TrainingRecord::class, 'student_id', 'cid');
    }

    public function trainingRecordsIns()
    {
        return $this->hasMany(TrainingRecord::class, 'instructor_id', 'cid');
    }

    public function evaluations()
    {
        return $this->hasMany(OTSEval::class, 'student_id', 'cid');
    }

    public function evaluationsIns()
    {
        return $this->hasMany(OTSEval::class, 'instructor_id', 'cid');
    }

    public function visits()
    {
        return $this->hasMany(Visit::class, 'cid', 'cid');
    }

    public function roles() {
        return $this->hasMany(Role::class, 'cid', 'cid');
    }

    public function getPrimaryRole()
    {
        for ($i = 1; $i <= 14; $i++) {
            if (RoleHelper::hasRole($this->cid, "ZHQ", "US$i")) {
                return $i;
            }
        }
        if ($this->facility()->atm == $this->cid) {
            return "ATM";
        }
        if ($this->facility()->datm == $this->cid) {
            return "DATM";
        }
        if ($this->facility()->ta == $this->cid) {
            return "TA";
        }
        if ($this->facility()->ec == $this->cid) {
            return "EC";
        }
        if ($this->facility()->fe == $this->cid) {
            return "FE";
        }
        if ($this->facility()->wm == $this->cid) {
            return "WM";
        }

        return false;
    }

    public function addToFacility($facility)
    {
        $oldfac = $this->facility;
        $facility = Facility::find($facility);
        $oldfac = Facility::find($oldfac);

        $this->facility = $facility->id;
        $this->facility_join = \DB::raw("NOW()");
        $this->save();

        if ($this->rating >= Helper::ratingIntFromShort("I1")) {
            SMFHelper::createPost(7262, 82,
                "User Addition: " . $this->fullname() . " (" . Helper::ratingShortFromInt($this->rating) . ") to " . $this->facility,
                "User " . $this->fullname() . " (" . $this->cid . "/" . Helper::ratingShortFromInt($this->rating) . ") was added to " . $this->facility . " and holds a higher rating.\n\nPlease check for demotion requirements.\n\n[url=https://www.vatusa.net/mgt/controller/" . $this->cid . "]Member Management[/url]");
        }

        $fc = 0;

        if ($oldfac->id != "ZZN" && $oldfac->id != "ZAE") {
            if ($oldfac->atm == $this->cid || $oldfac->datm == $this->cid) {
                EmailHelper::sendEmail(["vatusa2@vatusa.net"], "ATM or DATM discrepancy",
                    "emails.transfers.atm", ["user" => $this, "oldfac" => $oldfac]);
                $fc = 1;
            } elseif ($oldfac->ta == $this->cid) {
                EmailHelper::sendEmail(["vatusa3@vatusa.net"], "TA discrepancy", "emails.transfers.ta",
                    ["user" => $this, "oldfac" => $oldfac]);
                $fc = 1;
            } elseif ($oldfac->ec == $this->cid || $oldfac->wm == $this->cid || $oldfac->fe == $this->cid) {
                EmailHelper::sendEmail([$oldfac->id . "-atm@vatusa.net", $oldfac->id . "-datm@vatusa.net"],
                    "Staff discrepancy", "emails.transfers.otherstaff", ["user" => $this, "oldfac" => $oldfac]);
                $fc = 1;
            }
        }

        if ($fc) {
            SMFHelper::createPost(7262, 82,
                "Staff discrepancy on transfer: " . $this->fullname() . " (" . Helper::ratingShortFromInt($this->rating),
                "User " . $this->fullname() . " (" . $this->cid . "/" . Helper::ratingShortFromInt($this->rating) . ") was added to facility " . $this->facility . " but holds a staff position at " . $oldfac->id . ".\n\nPlease check for accuracy.\n\n[url=https://www.vatusa.net/mgt/controller/" . $this->cid . "]Member Management[/url] [url=https://www.vatusa.net/mgt/facility/" . $oldfac->id . "]Facility Management for Old Facility[/url] [url=https://www.vatusa.net/mgt/facility/" . $this->facility . "]Facility Management for New Facility[/url]");
        }

        if ($facility->active) {
            $welcome = $facility->welcome_text;
            $fac = $facility->id;
            EmailHelper::sendWelcomeEmail(
                [$this->email],
                "Welcome to " . $facility->name,
                'emails.user.welcome',
                [
                    'welcome' => $welcome,
                    'fname' => $this->fname,
                    'lname' => $this->lname
                ]
            );
            EmailHelper::sendEmail([
                "$fac-atm@vatusa.net",
                "$fac-datm@vatusa.net",
                "vatusa2@vatusa.net"
            ], "User added to facility", "emails.user.addedtofacility", [
                "name" => $this->fullname(),
                "cid" => $this->cid,
                "email" => $this->email,
                "rating" => Helper::ratingShortFromInt($this->rating),
                "facility" => $fac
            ]);

            $this->visits()->where('facility', $fac)->delete();

            Cache::forget("roster-$facility-home");
            Cache::forget("roster-$facility-both");
        }
    }

    public function removeFromFacility($by = "Automated", $msg = "None provided", $newfac = "ZAE")
    {
        if ($newfac == $this->facility) {
            return;
        }
        $facility = $this->facility;
        $region = $this->facility()->region;
        $facname = $this->facility()->name;

        if (!in_array($facility, ["ZAE", "ZZN", "ZZI"])) {
            EmailHelper::sendEmail(
                [$this->email, "$facility-atm@vatusa.net", "$facility-datm@vatusa.net", "vatusa2@vatusa.net"],
                "Removal from $facname",
                "emails.user.removed",
                [
                    'name' => $this->fname . " " . $this->lname,
                    'facility' => $this->facname,
                    'by' => $by === "Automated" ? $by : Helper::nameFromCID($by),
                    'msg' => $msg,
                    'facid' => $facility,
                    'region' => $region,
                    'rating' => Helper::ratingShortFromInt($this->rating),
                    'obsInactive' => $this->rating == 1 && Str::contains($msg,
                            ['inactive', 'inactivity', 'Inactive', 'Inactivity', 'activity', 'Activity'])
                ]
            );

            // Remove All roles
            foreach (Role::where('cid', $this->cid)->get() as $role) {
                // This looks silly, but it would take a huge refactor to avoid
                if (in_array($role->role, ['ATM', 'DATM', 'TA', 'FE', 'EC', 'WM'])) {
                    $spos = strtolower($role->role);
                    $fu = Facility::where('id', $role->facility)->first();
                    $fu->$spos = 0;
                    $fu->save();
                }
                $log = new Actions();
                $log->to = $this->cid;
                $log->log = $role->facility . " " . $role->role . " role revoked by $by";
                $log->save();
                $role->delete();
            }
            $moodle = new VATUSAMoodle();
            try {
                $moodle->unassignMentorRoles($this->cid);
            } catch (Exception $e) {
            }
        }

        if (is_numeric($by) && $by > 800000) {
            $byuser = User::find($by);
            $by = $byuser->fullname();
        }

        $log = new Actions();
        $log->from = 0;
        $log->to = $this->cid;
        $log->log = "Removed from $facname by $by: $msg";
        $log->save();

        /** Remove from visiting rosters if going to ZAE */
        if ($newfac == "ZAE" && count($this->visits()->get()) > 0) {
            $visitMsg = "Removed from home facility " . $facility . " for: " . $msg;
            $this->removeFromVisitingFacilities($visitMsg);
        }

        if ($newfac == "ZZN") {
            $this->flag_homecontroller = 0;
        }

        $this->facility_join = \DB::raw("NOW()");
        $this->facility = $newfac;
        $this->save();

        Cache::forget("roster-$facility-home");
        Cache::forget("roster-$facility-both");

        $t = new Transfers();
        $t->cid = $this->cid;
        $t->to = $newfac;
        $t->from = $facility;
        $t->reason = $msg;
        $t->status = 1;
        $t->actiontext = $msg;
        $t->save();

        if ($this->rating >= Helper::ratingIntFromShort("I1")) {
            SMFHelper::createPost(7262, 82,
                "User Removal: " . $this->fullname() . " (" . Helper::ratingShortFromInt($this->rating) . ") from " . $facility,
                "User " . $this->fullname() . " (" . $this->cid . "/" . Helper::ratingShortFromInt($this->rating) . ") was removed from $facility and holds a higher rating.  Please check for demotion requirements.  [url=https://www.vatusa.net/mgt/controller/" . $this->cid . "]Member Management[/url]");
        }
        // if ($this->rating >= Helper::ratingIntFromShort("I1"))
        // SMFHelper::createPost(7262, 82, "User Removal: " . $this->fullname() . " (" . Helper::ratingShortFromInt($this->rating) . ") from " . $facility, "User " . $this->fullname() . " (" . $this->cid . "/" . Helper::ratingShortFromInt($this->rating) . ") was removed from $facility and holds a higher rating.  Please check for demotion requirements.  [url=https://www.vatusa.net/mgt/controller/" . $this->cid . "]Member Management[/url]");
    }

    public function removeFromVisitingFacilities($reason) {
        foreach ($this->visits()->get() as $visit) {
            $log = new Actions();
            $log->from = 0;
            $log->to = $this->cid;
            $log->log = "User removed from {$visit->facility} visiting roster: {$reason}";
            $log->save();

            $facility = $visit->facility;
            $facname = Facility::find($facility)->name;

            EmailHelper::sendEmail(
                [$this->email, "$facility-atm@vatusa.net", "$facility-datm@vatusa.net", "vatusa2@vatusa.net"],
                "Removal from $facname Visiting Roster",
                "emails.user.removedVisit",
                [
                    'name' => $this->fname . " " . $this->lname,
                    'cid' => $this->cid,
                    'facility' => $facility,
                    'reason' => $reason,
                ]
            );

            $visit->delete();
        }
    }

    public function purge(
        $alltables = false
    )
    {
        //$this->delete();

        //TODO: Purge from All Tables
    }

    public function transferEligible(
        &$checks = null
    )
    {
        if ($checks === null) {
            $checks = [];
        }

        if (!is_array($checks)) {
            $checks = [];
        }

        $checks['homecontroller'] = 0;
        $checks['needbasic'] = 0;
        $checks['pending'] = 0;
        $checks['initial'] = 0;
        $checks['90days'] = 0;
        $checks['promo'] = 0;
        $checks['50hrs'] = 1;
        $checks['override'] = 0;
        $checks['is_first'] = 1;
        $checks['homecontroller'] = $this->flag_homecontroller;

        if($this->facility == "ZZI"){
            $checks['homecontroller'] = 0;
        }

        if (!$this->flag_needbasic) {
            $checks['needbasic'] = 1;
        }
        // 1 = check passed

        // Pending transfer request
        $transfer = Transfers::where('cid', $this->cid)->where('status', 0)->count();
        if (!$transfer) {
            $checks['pending'] = 1;
        }

        $checks['initial'] = 1;
        if (!in_array($this->facility, ["ZAE", "ZZN", "ZHQ"])) {
            if (Transfers::where('cid', $this->cid)->where('to', 'NOT LIKE', 'ZAE')->where('to', 'NOT LIKE',
                    'ZZN')->where('status', 1)->count() == 1) {
                if (Carbon::createFromFormat('Y-m-d H:i:s', $this->facility_join)->diffInDays(new Carbon()) <= 30) {
                    $checks['initial'] = 1;
                }
            } else {
                $checks['is_first'] = 0;
            }
        } else {
            $checks['is_first'] = 0;
        }
        $transfer = Transfers::where('cid', $this->cid)
            ->where('to', '!=', 'ZAE')
            ->where('to', '!=', 'ZZN')
            ->where('to', '!=', 'ZZI')
            ->where('status', 1)
            ->orderBy('created_at', 'DESC')
            ->first();
        if (!$transfer) {
            $checks['90days'] = 1;
        } else {
            $checks['days'] = Carbon::createFromFormat('Y-m-d H:i:s',
                $transfer->updated_at)->diffInDays(new Carbon());
            if ($checks['days'] >= 90) {
                $checks['90days'] = 1;
            } else {
                $checks['90days'] = 0;
            }
        }

        // added to visiting roster in last 60 days check
        $visiting = Visit::where('cid', $this->cid)
            ->orderBy('created_at', 'DESC')
            ->first();
        if (!$visiting) {
            $checks['60days'] = 1;
        } else {
            $checks['visitingDays'] = Carbon::createFromFormat('Y-m-d H:i:s', $visiting->updated_at)->diffInDays(new Carbon());
            if ($checks['visitingDays'] >= 60) {
                $checks['60days'] = 1;
            } else {
                $checks['60days'] = 0;
            }
        }

        // S1-C1 within 90 check
        $promotion = Promotions::where('cid', $this->cid)->where([
            ['to', '<=', Helper::ratingIntFromShort("C1")],
            ['created_at', '>=', \DB::raw("DATE(NOW() - INTERVAL 90 DAY)")]
        ])->whereRaw('promotions.to > promotions.from')->first();
        if ($promotion == null) {
            $checks['promo'] = 1;
        } else {
            $checks['promo'] = 0;
            $checks['promoDays'] = Carbon::createFromFormat('Y-m-d H:i:s', $promotion->created_at)->diffInDays(new Carbon());
        }

        // 50 hours consolidating current rating
        $ratingHours = VATSIMApi2Helper::fetchRatingHours($this->cid);
        if ($ratingHours == null) {
            $checks['50hrs'] = 1;
            $checks['ratingHours'] = 0;
        }
        else if($this->rating == Helper::ratingIntFromShort("S1") && $ratingHours['s1'] < 50){
            $checks['50hrs'] = 0;
            $checks['ratingHours'] = $ratingHours['s1'];
        }
        else if($this->rating == Helper::ratingIntFromShort("S2") && $ratingHours['s2'] < 50){
            $checks['50hrs'] = 0;
            $checks['ratingHours'] = $ratingHours['s2'];
        }
        else if($this->rating == Helper::ratingIntFromShort("S3") && $ratingHours['s3'] < 50){
            $checks['50hrs'] = 0;
            $checks['ratingHours'] = $ratingHours['s3'];
        }
        else if($this->rating == Helper::ratingIntFromShort("C1") && ($ratingHours['c1']+$ratingHours['c3']+$ratingHours['i1']+$ratingHours['i3']) < 50){
            $checks['50hrs'] = 0;
            $checks['ratingHours'] = $ratingHours['c1']+$ratingHours['c3']+$ratingHours['i1']+$ratingHours['i3'];
        }
        
        if (!in_array($this->facility, ["ZAE", "ZZI"])) {
            $checks['hasHome'] = 1;
        } else {
            $checks['hasHome'] = 0;
        }

        if ($this->rating >= Helper::ratingIntFromShort("S3")){
            $checks['hasRating'] = 1;
        } else {
            $checks['hasRating'] = 0;
        }

        if ($this->rating >= Helper::ratingIntFromShort("I1") && $this->rating <= Helper::ratingIntFromShort("I3")) {
            $checks['instructor'] = 0;
        } else {
            $checks['instructor'] = 1;
        }

        $facility = Facility::find($this->facility);
        $checks['staff'] = 1;
        if ($facility->atm == $this->cid) {
            $checks['staff'] = 0;
        }
        if ($facility->datm == $this->cid) {
            $checks['staff'] = 0;
        }
        if ($facility->ta == $this->cid) {
            $checks['staff'] = 0;
        }
        if ($facility->ec == $this->cid) {
            $checks['staff'] = 0;
        }
        if ($facility->fe == $this->cid) {
            $checks['staff'] = 0;
        }
        if ($facility->wm == $this->cid) {
            $checks['staff'] = 0;
        }

        // Exempt if in ZAE
        /*        if ($this->facility == "ZAE" && !$this->flag_needbasic && !$this->selectionEligible() && !Transfers::where('cid', $this->cid)->where('status',0)->count())
                    return true;*/

        if($checks ['60days'] && $checks['hasRating'] && $checks['hasHome'] && $checks['50hrs'] && $checks['needbasic'] && $checks['promo']){
            $checks['visiting'] = 1;
        } else {
            $checks['visiting'] = 0;
        }

        // Override flag
        if ($this->flag_xferOverride) {
            $checks['override'] = 1;
        } else {
            $checks['override'] = 0;
        }

        if ($checks['override']) {
            return true;
        }
        if (($checks['50hrs'] || !$checks['hasHome']) && $checks['instructor'] && $checks['staff'] && $checks['homecontroller'] && $checks['needbasic'] && $checks['pending'] && (($checks['is_first'] && $checks['initial']) || $checks['90days']) && $checks['promo']) {
            return true;
        } else {
            return false;
        }
    }

    public function toggleBasic()
    {
        $this->flag_needbasic = (($this->flag_needbasic) ? 0 : 1);
        $this->save();
    }

    public function selectionEligible()
    {
        if ($this->flag_homecontroller == 0 || $this->facility != "ZAE") {
            return false;
        }

        $passedBasic = ExamHelper::academyPassedExam($this->cid, "basic", 0, 6);
        if ($passedBasic && $this->flag_needbasic && $this->rating <= Helper::ratingIntFromShort("S1")) {
            $this->flag_needbasic = 0;
            $this->save();
        }

        return !$this->flag_needbasic && !Transfers::where('cid', $this->cid)->where('to',
                'NOT LIKE', 'ZAE')->where('to', 'NOT LIKE', 'ZZN')->exists() && $this->rating < Helper::ratingIntFromShort("S1");
    }

    public function promotionEligible()
    {
        if (!$this->flag_homecontroller) {
            Cache::set("promotionEligible-$this->cid", false);

            return false;
        }

        $result = false;
        if ($this->rating == Helper::ratingIntFromShort("OBS")) {
            $result = $this->isS1Eligible();
        }
        if ($this->rating == Helper::ratingIntFromShort("S1")) {
            $result = $this->isS2Eligible();
        }
        if ($this->rating == Helper::ratingIntFromShort("S2")) {
            $result = $this->isS3Eligible();
        }
        if ($this->rating == Helper::ratingIntFromShort("S3")) {
            $result = $this->isC1Eligible();
        }

        Cache::set("promotionEligible-$this->cid", $result);

        return $result;
    }

    public function isS1Eligible()
    {
        if ($this->rating > Helper::ratingIntFromShort("OBS")) {
            return false;
        }

        return !$this->flag_needbasic;
    }

    public function isS2Eligible()
    {
        if ($this->rating != Helper::ratingIntFromShort("S1")) {
            return false;
        }

        return ExamHelper::academyPassedExam($this->cid, "S2") || ExamHelper::academyPassedExam($this->cid, "S2_RCE");
    }

    public function isS3Eligible()
    {
        if ($this->rating != Helper::ratingIntFromShort("S2")) {
            return false;
        }

        return ExamHelper::academyPassedExam($this->cid, "S3") || ExamHelper::academyPassedExam($this->cid, "S3_RCE");

    }

    public function isC1Eligible()
    {
        if ($this->rating != Helper::ratingIntFromShort("S3")) {
            return false;
        }

        return ExamHelper::academyPassedExam($this->cid, "C1") || ExamHelper::academyPassedExam($this->cid, "C1_RCE");

    }

    public function lastActivityWebsite()
    {
        return $this->lastactivity->diffInDays(null);
    }

    public function lastActivityForum()
    {
        $f = \DB::connection('forum')->table("smf_members")->where("member_name", $this->cid)->first();

        return ($f) ? Carbon::createFromTimestamp($f->last_login)->diffInDays(null) : "Unknown";
    }

    public function lastPromotion()
    {
        return $this->hasMany(Promotions::class, 'cid', 'cid')->latest()->first();
    }

    public function isActive()
    {
        $website = false;
        $forum = false;
        if ($this->lastActivityWebsite() >= config('tattlers.staffacitivity.days')) {
            $website = true;
        }

        if ($this->lastActivityForum() >= config('tattlers.staffacitivity.days')) {
            $forum = true;
        }

        if ($forum || $website) {
            return true;
        }

        return false;
    }

    public function getTrainingActivitySparkline()
    {
        $vals = [];
        for ($i = 10; $i >= 0; $i--) {
            $vals[] = $this->trainingRecordsIns()->selectRaw("SUM(TIME_TO_SEC(duration)) AS sum, DATE_FORMAT(session_date, '%Y-%U') AS week")
                ->where(DB::raw("DATE_FORMAT(session_date, '%Y-%U')"), '=',
                    Carbon::now()->subWeeks($i)->format('Y-W'))->groupBy(['week'])->orderBy('week',
                    'ASC')->pluck('sum')->map(function ($v) {
                    return floor($v / 3600);
                })->pop() ?? 0;
        }

        return implode(",", $vals);
    }

    public function checkPromotionCriteria(
        &$trainingRecordStatus,
        &$otsEvalStatus,
        &$examPosition,
        &$dateOfExam,
        &$evalId
    )
    {
        $trainingRecordStatus = 0;
        $otsEvalStatus = 0;

        $dateOfExam = null;
        $examPosition = null;
        $evalId = null;

        $evals = $this->evaluations;
        $numPass = 0;
        $numFail = 0;

        if ($evals) {
            foreach ($evals as $eval) {
                if ($eval->form->rating_id == $this->rating + 1) {
                    if ($eval->result) {
                        $dateOfExam = $eval->exam_date;
                        $examPosition = $eval->exam_position;
                        $evalId = $eval->id;
                        $numPass++;
                    } else {
                        $numFail++;
                    }
                }
            }
            if ($numPass) {
                $otsEvalStatus = 1;
            } elseif ($numFail) {
                $otsEvalStatus = 2;
            }
        }

        switch (Helper::ratingShortFromInt($this->rating + 1)) {
            case 'S1':
                $pos = "GND";
                break;
            case 'S2':
                $pos = "TWR";
                break;
            case 'S3':
                $pos = "APP";
                break;
            case 'C1':
                $pos = "CTR";
                break;
            default:
                $pos = "NA";
                break;
        }
        if ($this->trainingRecords()->where([
            ['position', 'like', "%$pos"],
            'ots_status' => 1
        ])->exists()) {
            $trainingRecordStatus = 1;
        }
    }
}

