<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Policy extends Model
{
    protected $dates = ['created_at', 'updated_at', 'effective_date'];

    public const PERMS_ALL = 0;
    public const PERMS_HOME = 1;
    public const PERMS_WM = 2;
    public const PERMS_EC = 3;
    public const PERMS_FE = 4;
    public const PERMS_MTR = 5;
    public const PERMS_INS = 6;
    public const PERMS_TA = 7;
    public const PERMS_DATM = 8;
    public const PERMS_ATM = 9;
    public const PERMS_VATUSA = 10;

    public function category() {
        return $this->belongsTo(PolicyCategory::class,'policy_category');
    }
}
