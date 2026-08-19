<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class liveSupportMessage extends Model
{
    use HasFactory;

    protected $casts = [
        'created_at' => 'datetime'
    ];

    protected $appends = ['readable_created_at'];

    protected $fillable = ['topic_id', 'user_id', 'agent_id', 'message'];

    public function getReadableCreatedAtAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * Get the user who sent this message
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the agent/admin who responded (if any)
     */
    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    /**
     * Get the support topic this message belongs to
     */
    public function topic()
    {
        return $this->belongsTo(liveSupportTopic::class, 'topic_id');
    }

    /**
     * Scope to get messages for a specific conversation
     */
    public function scopeForConversation($query, $topicId, $userId)
    {
        return $query->where('topic_id', $topicId)
            ->where('user_id', $userId)
            ->orderBy('created_at', 'asc');
    }

    /**
     * Check if this message was sent by an agent/admin
     */
    public function isFromAgent()
    {
        return !is_null($this->agent_id);
    }

    /**
     * Check if this message was sent by the user
     */
    public function isFromUser()
    {
        return is_null($this->agent_id);
    }
}
