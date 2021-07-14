<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KnowledgebaseQuestions extends Model
{
    protected $table = "knowledgebase_questions";

    public function category() {
        return $this->hasOne(KnowledgebaseCategories::class, 'id', 'category_id');
    }
}