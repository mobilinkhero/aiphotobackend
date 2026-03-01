<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\AppSetting;
use App\Http\Controllers\AiEnhanceController;

// Apply App Secret authentication to all API endpoints
Route::middleware(['app.secret', 'api.logger'])->group(function () {

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
            'initial_free_coins',
            // Feature flags
            'feature_enhance_enabled',
            'feature_enhance_premium',
            'feature_enhance_coins',
            'feature_restore_enabled',
            'feature_restore_premium',
            'feature_restore_coins',
            'feature_face_enabled',
            'feature_face_premium',
            'feature_face_coins',
            'feature_upscale_enabled',
            'feature_upscale_premium',
            'feature_upscale_coins',
            'feature_colorize_enabled',
            'feature_colorize_premium',
            'feature_colorize_coins',
            'feature_background_enabled',
            'feature_background_premium',
            'feature_background_coins',
        ])->get()->pluck('value', 'key');

        $featuresData = [
            [
                'id' => 'enhance',
                'title' => 'Enhance Photo',
                'description' => 'Auto fix resolution & color',
                'icon' => 'auto_fix_high_rounded',
                'color' => '0xFF448AFF', // blueAccent
                'enabled' => $settings->get('feature_enhance_enabled', '1') == '1',
                'isPremium' => $settings->get('feature_enhance_premium', '0') == '1',
                'coins' => (int) $settings->get('feature_enhance_coins', '1'),
                'benefits' => [],
                'beforeUrl' => 'assets/images/demos/enhance_before.jpg',
                'afterUrl' => 'assets/images/demos/enhance_after.jpg',
            ],
            [
                'id' => 'restore',
                'title' => 'Restore Old Photo',
                'description' => 'Repair scratches & blur',
                'icon' => 'history_rounded',
                'color' => '0xFFFFAB40', // orangeAccent
                'enabled' => $settings->get('feature_restore_enabled', '1') == '1',
                'isPremium' => $settings->get('feature_restore_premium', '1') == '1',
                'coins' => (int) $settings->get('feature_restore_coins', '1'),
                'benefits' => [
                    'Remove deep scratches & folds',
                    'Restore faded facial features',
                    'AI neural grain removal',
                    'Clean background noise'
                ],
                'beforeUrl' => 'assets/images/demos/restore_before.jpg',
                'afterUrl' => 'assets/images/demos/restore_after.jpg',
            ],
            [
                'id' => 'face',
                'title' => 'Face Enhance',
                'description' => 'Perfect facial details',
                'icon' => 'face_retouching_natural_rounded',
                'color' => '0xFFFF4081', // pinkAccent
                'enabled' => $settings->get('feature_face_enabled', '1') == '1',
                'isPremium' => $settings->get('feature_face_premium', '0') == '1',
                'coins' => (int) $settings->get('feature_face_coins', '1'),
                'benefits' => [],
                'beforeUrl' => 'assets/images/demos/face_before.jpg',
                'afterUrl' => 'assets/images/demos/face_after.jpg',
            ],
            [
                'id' => 'upscale',
                'title' => 'Upscale to HD',
                'description' => '4x Resolution boost',
                'icon' => 'high_quality_rounded',
                'color' => '0xFF69F0AE', // greenAccent
                'enabled' => $settings->get('feature_upscale_enabled', '1') == '1',
                'isPremium' => $settings->get('feature_upscale_premium', '1') == '1',
                'coins' => (int) $settings->get('feature_upscale_coins', '1'),
                'benefits' => [
                    '4X Detail enhancement',
                    'Crystal clear 4K output',
                    'Preserve natural textures',
                    'Super-resolution AI technology'
                ],
                'beforeUrl' => 'assets/images/demos/upscale_before.jpg',
                'afterUrl' => 'assets/images/demos/upscale_after.jpg',
            ],
            [
                'id' => 'colorize',
                'title' => 'Colorize Photo',
                'description' => 'Add life to B&W photos',
                'icon' => 'palette_rounded',
                'color' => '0xFF7C4DFF', // deepPurpleAccent
                'enabled' => $settings->get('feature_colorize_enabled', '1') == '1',
                'isPremium' => $settings->get('feature_colorize_premium', '0') == '1',
                'coins' => (int) $settings->get('feature_colorize_coins', '1'),
                'benefits' => [],
                'beforeUrl' => 'assets/images/demos/colorize_before.jpg',
                'afterUrl' => 'assets/images/demos/colorize_after.jpg',
            ],
            [
                'id' => 'background',
                'title' => 'Background Fix',
                'description' => 'Remove & enhance BG',
                'icon' => 'image_aspect_ratio_rounded',
                'color' => '0xFF18FFFF', // cyanAccent
                'enabled' => $settings->get('feature_background_enabled', '1') == '1',
                'isPremium' => $settings->get('feature_background_premium', '1') == '1',
                'coins' => (int) $settings->get('feature_background_coins', '1'),
                'benefits' => [
                    'Smart object-aware removal',
                    'Natural portrait bokeh',
                    'One-tap background swap',
                    'Professional depth of field'
                ],
                'beforeUrl' => 'assets/images/demos/background_before.jpg',
                'afterUrl' => 'assets/images/demos/background_after.jpg',
            ],
        ];

        // Filter out features completely disabled on the server side
        $featuresData = array_values(array_filter($featuresData, function ($f) {
            return $f['enabled'];
        }));

        return response()->json([
            'success' => true,
            'config' => $settings,
            'features' => $featuresData,
        ]);
    });

    // ─────────────────────────────────────────────────────────────────────
    //  Tool-Specific API Endpoints
    //  Wrapped in Rate Limiting (Throttle) to prevent abuse:
    //  e.g., maximum 3 requests per day (1440 minutes) per IP for free users
    // ─────────────────────────────────────────────────────────────────────
    Route::middleware(['throttle:1000,1'])->group(function () {
        Route::post('/enhance', [AiEnhanceController::class, 'processEnhance']);
        Route::post('/colorize', [AiEnhanceController::class, 'processColorize']);
        Route::post('/restore', [AiEnhanceController::class, 'processRestore']);
        Route::post('/face', [AiEnhanceController::class, 'processFace']);
        Route::post('/upscale', [AiEnhanceController::class, 'processUpscale']);
        Route::post('/background', [AiEnhanceController::class, 'processBackground']);
    });
});
