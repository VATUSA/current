<?php
/**
 *
 * @author Blake Nahin <blake@zseartcc.org>
 */

namespace App\Classes;


use App\ReturnPaths;

class ULSHelper
{
    /**
     * @param $facility
     *
     * @return ReturnPaths
     */
    public static function getReturnPaths($facility)
    {
        return ReturnPaths::where('facility_id', $facility)->orderBy('order', 'ASC')->get();
    }
}