<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Visit
 * @package App
 *
 * @SWG\Definition(
 *     type="object",
 *     @SWG\Property(property="id", type="integer"),
 *     @SWG\Property(property="cid", type="integer"),
 *     @SWG\Property(property="facility", type="string"),
 *     @SWG\Property(property="active", type="integer", description="0 = inactive, 1 = active"),
 *     @SWG\Property(property="created_at", type="string"),
 * )
 */
class Visit extends Model
{
    protected $table = 'visits';

    public function user()
    {
        return $this->belongsTo('App\User', 'cid', 'cid');
    }

    public function fac()
    {
        return $this->hasOne("App\Facility", "id", "facility");
    }
}
