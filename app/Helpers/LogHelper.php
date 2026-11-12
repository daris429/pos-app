<?php

namespace App\Helpers;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class LogHelper
{
    public static function log($action, $description)
    {
        ActivityLog::create([
            'user_id'     => Auth::id(),
            'user_name'   => Auth::check() ? Auth::user()->name : 'System',
            'action'      => $action,
            'description' => $description,
        ]);
    }
}