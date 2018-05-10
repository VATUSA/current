<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class KnowledgebaseCategories extends Model
{
    protected $table = "knowledgebase_categories";

    public function questions() {
        return $this->hasMany('App\KnowledgebaseQuestions', 'category_id', 'id');
    }
}