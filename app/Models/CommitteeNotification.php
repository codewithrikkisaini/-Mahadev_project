<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommitteeNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'message',
        'type',
        'link',
        'is_read',
    ];

    protected function casts(): array
    {
        return [
            'is_read' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Send notification to a specific user.
     */
    public static function sendToUser(int $userId, string $title, string $message, string $type = 'general', ?string $link = null): self
    {
        return static::create([
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'link' => $link,
            'is_read' => false,
        ]);
    }

    /**
     * Send notification to all active members.
     */
    public static function sendToAllMembers(string $title, string $message, string $type = 'announcement', ?string $link = null): void
    {
        $memberUserIds = User::where('role', 'member')->where('status', 'active')->pluck('id');
        foreach ($memberUserIds as $userId) {
            static::create([
                'user_id' => $userId,
                'title' => $title,
                'message' => $message,
                'type' => $type,
                'link' => $link,
                'is_read' => false,
            ]);
        }
    }
}
