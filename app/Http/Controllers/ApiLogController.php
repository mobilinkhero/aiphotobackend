<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ApiLog;

class ApiLogController extends Controller
{
    public function index()
    {
        // Don't fetch huge body data here to keep lists light
        $logs = ApiLog::latest()->limit(100)->get(['id', 'method', 'path', 'status_code', 'duration', 'created_at']);

        $total = ApiLog::count();
        $success = ApiLog::where('status_code', 200)->count();
        $errors = ApiLog::whereBetween('status_code', [400, 599])->count();
        $avgDuration = round(ApiLog::avg('duration') * 1000);

        return view('admin.apilogs.index', compact('logs', 'total', 'success', 'errors', 'avgDuration'));
    }

    public function show(ApiLog $log)
    {
        return response()->json($log);
    }
}
