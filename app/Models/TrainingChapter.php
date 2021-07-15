<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrainingChapter
    extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'training_chapters';

    public $timestamps = false;

    public function block() {
        return $this->hasOne(TrainingBlock::class, 'id', 'blockid');
    }
}