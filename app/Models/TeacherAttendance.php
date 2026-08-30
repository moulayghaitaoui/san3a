<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeacherAttendance extends Model
{
    protected $fillable = [
        'upload_log_id',
        'state_id',
        'reference_number',
        'full_name',
        'status_type',
        'specialty',
        'rank',
        'institution',
        'is_present',
        'is_absent',
        'absence_reason'
    ];

    public function uploadLog()
    {
        return $this->belongsTo(UploadLog::class);
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }
}
