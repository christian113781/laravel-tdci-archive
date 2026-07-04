<?php

namespace App\Http\Controllers;

use App\Models\Log;
use Illuminate\Http\Request;

class AdminControllerLogs extends Controller
{
    public function index(Request $request)
    {
        $logs = Log::with(['user', 'archive'])
            ->orderByDesc('created_at')
            ->get();

        return view('admin.admin_logs', compact('logs'));
    }
}
