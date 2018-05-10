<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class TrainingBlock
    extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'training_blocks';

    public $timestamps = false;

    public function chapters()
    {
        return $this->hasMany('App\TrainingChapter', 'blockid')->orderBy("order");
    }

    public function userCompleted($cid)
    {
        $chapters = $this->chapters()->get();
        foreach ($chapters as $chapter) {
            if (TrainingProgress::where('cid',$cid)->where('chapterid', $chapter->id)->count() < 1)
                return false;
        }

        return true;
    }
}