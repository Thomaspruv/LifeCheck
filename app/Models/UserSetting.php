<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSetting extends Model
{
    protected $fillable = [
        'user_id',
        'checkin_reminder_time',
        'reminder_enabled',
        'week_start',
        'theme',
        'timezone',
    ];

    protected function casts(): array
    {
        return [
            'checkin_reminder_time' => 'string',
            'reminder_enabled' => 'boolean',
        ];
    }

    /**
     * Get the user that owns the settings.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
