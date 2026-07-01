<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Criteria extends Model
{
    protected $table = 'criteria';
    
    protected $fillable = ['name', 'type', 'description'];

    public function subCriteria()
    {
        return $this->hasMany(SubCriteria::class, 'criteria_id');
    }
}
