<?php
/**
 * Created by PhpStorm.
 * User: Dimitri Trofimuk
 * Date: 10/08/2015
 * Time: 10:05 PM
 */
namespace App;

use Illuminate\Database\Eloquent\Model;

class RoleTitle extends Model {
    protected $table = 'role_titles';
    public $incrementing = false;
}
