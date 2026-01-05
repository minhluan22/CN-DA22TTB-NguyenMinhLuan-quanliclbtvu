<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Violation extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'club_id',
        'regulation_id',
        'description',
        'severity',
        'violation_date',
        'recorded_by',
        'status',
        'discipline_type',
        'discipline_reason',
        'discipline_period_start',
        'discipline_period_end',
        'processed_by',
        'processed_at',
    ];

    protected $casts = [
        'violation_date' => 'datetime',
        'discipline_period_start' => 'date',
        'discipline_period_end' => 'date',
        'processed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function club()
    {
        return $this->belongsTo(Club::class);
    }

    public function regulation()
    {
        return $this->belongsTo(Regulation::class);
    }

    public function recorder()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function processor()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
}
