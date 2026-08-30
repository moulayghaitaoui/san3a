<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UploadLog extends Model
{
    protected $fillable = ['file_name', 'state_id', 'status', 'records_count'];

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function teacherAttendances()
    {
        return $this->hasMany(TeacherAttendance::class);
    }
}
