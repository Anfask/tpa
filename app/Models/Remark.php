<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Remark extends Model
{
    protected $fillable = ['inspector_id', 'teacher_id', 'content', 'is_private'];

    public function inspector()
    {
        return $this->belongsTo(User::class, 'inspector_id');
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }
}
