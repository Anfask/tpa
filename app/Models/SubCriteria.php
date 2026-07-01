<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubCriteria extends Model
{
    protected $table = 'sub_criteria';

    protected $fillable = ['criteria_id', 'name', 'description'];

    public function criteria()
    {
        return $this->belongsTo(Criteria::class, 'criteria_id');
    }

    public function questions()
    {
        return $this->hasMany(Question::class, 'sub_criteria_id');
    }
}
