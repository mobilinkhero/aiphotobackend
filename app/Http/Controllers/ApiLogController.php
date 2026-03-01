<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ApiLog;

class ApiLogController extends Controller
{
    public function index()
    {
        $logs = ApiLog::latest()->limit(100)->get();

        $total = $logs->count();
        $success = $logs->where('status_code', 200)->count();
        $errors = $logs->whereBetween('status_code', [400, 599])->count();
        $avgDuration = round($logs->avg('duration') * 1000); // convert seconds to ms

        return view('admin.apilogs.index', compact('logs', 'total', 'success', 'errors', 'avgDuration'));
    }
}
