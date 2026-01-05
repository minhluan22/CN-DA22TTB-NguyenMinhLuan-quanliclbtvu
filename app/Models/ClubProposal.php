<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClubProposal extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'club_name',
        'field',
        'objectives',
        'reason',
        'planned_activities',
        'expected_members',
        'advisor_name',
        'advisor_email',
        'proposer_name',
        'proposer_email',
        'proposer_student_code',
        'member_list_file',
        'activity_plan_file',
        'status',
        'admin_notes',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
