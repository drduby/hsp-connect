<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Request;

class ActivityLogger
{
    public static function log(string $action, string $description, ?int $userId = null, ?string $ip = null): void
    {
        try {
            ActivityLog::create([
                'action' => $action,
                'description' => $description,
                'user_id' => $userId ?? (auth()->check() ? auth()->id() : null),
                'ip_address' => $ip ?? Request::ip(),
            ]);
        } catch (\Throwable) {
            // Never crash the app for a logging failure
        }
    }
}
