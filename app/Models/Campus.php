<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Campus extends Model
{
    protected $fillable = ['name', 'address'];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function classes()
    {
        return $this->hasMany(CampusClass::class, 'campus_id');
    }

    public function inspections()
    {
        return $this->hasMany(Inspection::class, 'campus_id');
    }
}
