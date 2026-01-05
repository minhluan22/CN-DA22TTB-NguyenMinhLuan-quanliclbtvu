<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'body',
        'sender_id',
        'is_public',
        'type',
        'target_type',
        'target_ids',
        'sent_at',
        'scheduled_at',
        'status',
        'notification_source',
        'club_id'
    ];

    protected $casts = [
        'target_ids' => 'array',
        'sent_at' => 'datetime',
        'scheduled_at' => 'datetime',
        'is_public' => 'boolean',
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function recipients()
    {
        return $this->hasMany(NotificationRecipient::class);
    }

    public function readRecipients()
    {
        return $this->hasMany(NotificationRecipient::class)->where('is_read', true);
    }

    public function unreadRecipients()
    {
        return $this->hasMany(NotificationRecipient::class)->where('is_read', false);
    }

    public function club()
    {
        return $this->belongsTo(Club::class);
    }
}
