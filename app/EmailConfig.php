<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class EmailConfig extends Model
{
    public $timestamps = false;
    protected $primaryKey = "address";
    public $incrementing = false;
    protected $table = "email_config";

    public static $configStatic = "static";
    public static $configUser = "user";

    /**
     * @return bool
     */
    public function isStatic() {
        if ($this->config === static::$configStatic) {
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function isUser() {
        return !$this->isStatic();
    }
}
