<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\AppSetting;
use App\Http\Controllers\AiEnhanceController;

// Apply App Secret authentication to all API endpoints
Route::middleware(['app.secret'])->group(function () {

    // ─────────────────────────────────────────────────────────────────────
    //  GET /api/config
    //  Returns public app settings to the mobile app.
    //  Sensitive keys (ai_api_key) are intentionally excluded.
    // ─────────────────────────────────────────────────────────────────────
    Route::get('/config', function (Request $request) {
        $settings = AppSetting::whereIn('key', [
            'admob_android_app_id',
            'admob_banner_unit_id',
            'admob_interstitial_unit_id',
            'admob_rewarded_unit_id',
            'admob_app_open_unit_id',
            'admob_native_unit_id',
            'admob_enabled',
            'maintenance_mode',
            'minimum_app_version',
            'app_store_url',
        ])->get()->pluck('value', 'key');

        return response()->json([
            'success' => true,
            'config' => $settings,
        ]);
    });

    // ─────────────────────────────────────────────────────────────────────
    //  Tool-Specific API Endpoints
    //  Wrapped in Rate Limiting (Throttle) to prevent abuse:
    //  e.g., maximum 3 requests per day (1440 minutes) per IP for free users
    // ─────────────────────────────────────────────────────────────────────
    Route::middleware(['throttle:3,1440'])->group(function () {
        Route::post('/enhance', [AiEnhanceController::class, 'processEnhance']);
        Route::post('/colorize', [AiEnhanceController::class, 'processColorize']);
        Route::post('/restore', [AiEnhanceController::class, 'processRestore']);
        Route::post('/face', [AiEnhanceController::class, 'processFace']);
        Route::post('/upscale', [AiEnhanceController::class, 'processUpscale']);
        Route::post('/background', [AiEnhanceController::class, 'processBackground']);
    });
});
