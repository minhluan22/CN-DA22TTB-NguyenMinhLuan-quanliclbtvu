<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'title','club_id','description','start_at','end_at','location','status','approval_status',
        'violation_notes','violation_type','violation_severity','violation_status',
        'violation_detected_at','violation_recorded_by',
        'created_by','deleted_at','deleted_by',
        'activity_type','goal','expected_participants','expected_budget','attachment'
    ];

    public function club()
    {
        return $this->belongsTo(Club::class);
    }

    public function registrations()
    {
        return $this->hasMany(EventRegistration::class, 'event_id');
    }
}
