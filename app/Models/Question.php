<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $fillable = ['sub_criteria_id', 'question_text', 'max_score', 'order_index'];

    public function subCriteria()
    {
        return $this->belongsTo(SubCriteria::class, 'sub_criteria_id');
    }
}
