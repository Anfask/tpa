<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inspection extends Model
{
    protected $fillable = [
        'inspector_id',
        'type',
        'teacher_id',
        'admin_id',
        'campus_id',
        'class_id',
        'score',
        'raw_data'
    ];

    protected $casts = [
        'raw_data' => 'array'
    ];

    public function inspector()
    {
        return $this->belongsTo(User::class, 'inspector_id');
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function campus()
    {
        return $this->belongsTo(Campus::class, 'campus_id');
    }

    public function campusClass()
    {
        return $this->belongsTo(CampusClass::class, 'class_id');
    }
}
