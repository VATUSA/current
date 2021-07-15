<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KnowledgebaseCategories extends Model
{
    protected $table = "knowledgebase_categories";

    public function questions() {
        return $this->hasMany(KnowledgebaseQuestions::class, 'category_id', 'id');
    }
}