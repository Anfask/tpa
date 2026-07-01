<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CampusClass extends Model
{
    protected $table = 'classes';

    protected $fillable = ['name', 'campus_id'];

    public function campus()
    {
        return $this->belongsTo(Campus::class);
    }
}
