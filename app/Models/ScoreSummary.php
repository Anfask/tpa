<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScoreSummary extends Model
{
    protected $table = 'score_summaries';

    protected $fillable = [
        'entity_type',
        'teacher_id',
        'admin_id',
        'campus_id',
        'period_type',
        'period_key',
        'average_score',
        'inspection_count'
    ];

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
}
