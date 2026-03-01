<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ApiLogController extends Controller
{
    public function index()
    {
        // Placeholder data until API logging is connected
        $logs = collect([
            ['id' => 1, 'method' => 'POST', 'endpoint' => '/api/enhance', 'status' => 200, 'duration_ms' => 1240, 'ip' => '192.168.1.10', 'created_at' => now()->subMinutes(2)],
            ['id' => 2, 'method' => 'GET', 'endpoint' => '/api/config', 'status' => 200, 'duration_ms' => 45, 'ip' => '192.168.1.11', 'created_at' => now()->subMinutes(5)],
            ['id' => 3, 'method' => 'POST', 'endpoint' => '/api/enhance', 'status' => 500, 'duration_ms' => 3020, 'ip' => '10.0.0.5', 'created_at' => now()->subMinutes(15)],
            ['id' => 4, 'method' => 'GET', 'endpoint' => '/api/config', 'status' => 200, 'duration_ms' => 38, 'ip' => '192.168.1.22', 'created_at' => now()->subMinutes(20)],
            ['id' => 5, 'method' => 'POST', 'endpoint' => '/api/enhance', 'status' => 200, 'duration_ms' => 980, 'ip' => '192.168.1.15', 'created_at' => now()->subMinutes(35)],
            ['id' => 6, 'method' => 'POST', 'endpoint' => '/api/enhance', 'status' => 422, 'duration_ms' => 120, 'ip' => '10.0.0.8', 'created_at' => now()->subHours(1)],
        ]);

        $total = $logs->count();
        $success = $logs->where('status', 200)->count();
        $errors = $logs->whereIn('status', [500, 422, 400])->count();
        $avgDuration = round($logs->avg('duration_ms'));

        return view('admin.apilogs.index', compact('logs', 'total', 'success', 'errors', 'avgDuration'));
    }
}
