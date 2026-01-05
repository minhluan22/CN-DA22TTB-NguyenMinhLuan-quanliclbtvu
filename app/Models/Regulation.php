<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Regulation extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code',
        'title',
        'content',
        'scope',
        'club_id',
        'severity',
        'status',
        'issued_date',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'issued_date' => 'date',
    ];

    public function club()
    {
        return $this->belongsTo(Club::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function violations()
    {
        return $this->hasMany(Violation::class);
    }
}
