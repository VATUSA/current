<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class KnowledgebaseQuestions extends Model
{
    protected $table = "knowledgebase_questions";

    public function category() {
        return $this->hasOne('App\KnowledgebaseCategories', 'id', 'category_id');
    }
}