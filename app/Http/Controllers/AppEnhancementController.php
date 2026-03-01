<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AppEnhancementController extends Controller
{
    public function index()
    {
        // Placeholder data until AI pipeline is connected
        $enhancements = collect([
            ['id' => 1, 'tool' => 'Enhance Photo', 'status' => 'success', 'device' => 'Pixel 7', 'created_at' => now()->subMinutes(5)],
            ['id' => 2, 'tool' => 'Face Enhance', 'status' => 'success', 'device' => 'Samsung S23', 'created_at' => now()->subMinutes(12)],
            ['id' => 3, 'tool' => 'Upscale to HD', 'status' => 'failed', 'device' => 'OnePlus 11', 'created_at' => now()->subMinutes(30)],
            ['id' => 4, 'tool' => 'Colorize Photo', 'status' => 'success', 'device' => 'Pixel 8 Pro', 'created_at' => now()->subMinutes(45)],
            ['id' => 5, 'tool' => 'Restore Old Photo', 'status' => 'success', 'device' => 'Xiaomi 13', 'created_at' => now()->subHours(1)],
            ['id' => 6, 'tool' => 'Background Fix', 'status' => 'pending', 'device' => 'Google Pixel 6', 'created_at' => now()->subHours(2)],
        ]);

        $total = $enhancements->count();
        $successful = $enhancements->where('status', 'success')->count();
        $failed = $enhancements->where('status', 'failed')->count();

        return view('admin.enhancements.index', compact('enhancements', 'total', 'successful', 'failed'));
    }
}
