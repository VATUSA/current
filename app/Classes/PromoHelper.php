<?php
namespace App\Classes;

use App\User;
use App\Classes\CertHelper;
use App\Promotions;
use App\Actions;

class PromoHelper
{
    public static function handle($cid, $ins, $to, $data) {
        $user = User::where('cid', $cid)->first();

        if(CertHelper::changeRating($cid, $to, false) === 0) {
            return 0;
        } else {
            $promo = new Promotions;
            $promo->cid = $cid;
            $promo->grantor = $ins;
            $promo->to = $to;
            $promo->from = $user->rating;
            $promo->exam = ((isset($data['exam']))?$data['exam']:'0000-00-00');
            $promo->examiner = ((isset($data['examiner'])?$data['examiner']:0));
            $promo->position = ((isset($data['position'])?$data['position']:''));
            $promo->save();

            $log = new Actions;
            $log->to = $cid;
            $log->from = $ins;
            $log->log = "Rating Change: ".$user->urating->short." to ".\App\Classes\Helper::ratingShortFromInt($to)." issued by ".Helper::nameFromCID($ins);
            $log->save();

            $user->rating = $to;
            $user->save();

            return 1;
        }
    }
}