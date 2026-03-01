<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    private $defaultSettings = [
        'admob_android_app_id' => ['type' => 'text', 'desc' => 'AdMob Android App ID', 'group' => 'admob'],
        'admob_banner_unit_id' => ['type' => 'text', 'desc' => 'AdMob Banner Unit ID', 'group' => 'admob'],
        'admob_interstitial_unit_id' => ['type' => 'text', 'desc' => 'AdMob Interstitial Unit ID', 'group' => 'admob'],
        'admob_rewarded_unit_id' => ['type' => 'text', 'desc' => 'AdMob Rewarded Unit ID', 'group' => 'admob'],
        'admob_app_open_unit_id' => ['type' => 'text', 'desc' => 'AdMob App Open Unit ID', 'group' => 'admob'],
        'admob_native_unit_id' => ['type' => 'text', 'desc' => 'AdMob Native / Video Unit ID', 'group' => 'admob'],
        'admob_enabled' => ['type' => 'boolean', 'desc' => 'Enable All Ads Globally', 'group' => 'admob'],

        'maintenance_mode' => ['type' => 'boolean', 'desc' => 'Enable Maintenance Mode (Blocks users)', 'group' => 'system'],
        'minimum_app_version' => ['type' => 'text', 'desc' => 'Minimum forced app version (e.g. 1.0.1)', 'group' => 'system'],
        'app_store_url' => ['type' => 'text', 'desc' => 'App/Play Store URL', 'group' => 'system'],

        'ai_api_key' => ['type' => 'password', 'desc' => 'API Key (Replicate/Fal)', 'group' => 'ai'],
        'ai_provider' => ['type' => 'text', 'desc' => 'Active AI Provider (e.g. replicate)', 'group' => 'ai'],
        'ai_max_concurrent' => ['type' => 'text', 'desc' => 'Max Concurrent Generations', 'group' => 'ai'],

        // Feature Toggles (App UI)
        'feature_enhance_enabled' => ['type' => 'boolean', 'desc' => 'Enable Photo Enhance', 'group' => 'features'],
        'feature_enhance_premium' => ['type' => 'boolean', 'desc' => 'Make Photo Enhance Premium', 'group' => 'features'],
        'feature_restore_enabled' => ['type' => 'boolean', 'desc' => 'Enable Restore Old Photo', 'group' => 'features'],
        'feature_restore_premium' => ['type' => 'boolean', 'desc' => 'Make Restore Premium', 'group' => 'features'],
        'feature_face_enabled' => ['type' => 'boolean', 'desc' => 'Enable Face Enhance', 'group' => 'features'],
        'feature_face_premium' => ['type' => 'boolean', 'desc' => 'Make Face Enhance Premium', 'group' => 'features'],
        'feature_upscale_enabled' => ['type' => 'boolean', 'desc' => 'Enable Upscale to HD', 'group' => 'features'],
        'feature_upscale_premium' => ['type' => 'boolean', 'desc' => 'Make Upscale Premium', 'group' => 'features'],
        'feature_colorize_enabled' => ['type' => 'boolean', 'desc' => 'Enable Colorize Photo', 'group' => 'features'],
        'feature_colorize_premium' => ['type' => 'boolean', 'desc' => 'Make Colorize Premium', 'group' => 'features'],
        'feature_background_enabled' => ['type' => 'boolean', 'desc' => 'Enable Background Fix', 'group' => 'features'],
        'feature_background_premium' => ['type' => 'boolean', 'desc' => 'Make Background Fix Premium', 'group' => 'features'],
    ];

    private function syncDefaults()
    {
        foreach ($this->defaultSettings as $key => $meta) {
            AppSetting::firstOrCreate(
                ['key' => $key],
                ['value' => ($meta['type'] == 'boolean') ? '0' : '', 'description' => $meta['desc']]
            );
        }
    }

    private function renderSettings($group, $title, $description, $updateRoute)
    {
        $this->syncDefaults();

        // Filter keys by the requested group
        $groupKeys = array_keys(array_filter($this->defaultSettings, function ($m) use ($group) {
            return $m['group'] === $group;
        }));
        $settings = AppSetting::whereIn('key', $groupKeys)->get();
        $defaultSettings = $this->defaultSettings;

        return view('admin.settings', compact('settings', 'defaultSettings', 'title', 'description', 'updateRoute'));
    }

    public function systemSettings()
    {
        return $this->renderSettings('system', 'System Configuration', 'Manage core app behaviors, version requirements, and kill-switches.', 'admin.settings.system.update');
    }

    public function admobSettings()
    {
        return $this->renderSettings('admob', 'AdMob Configuration', 'Control your mobile app monetization remotely across all devices.', 'admin.settings.admob.update');
    }

    public function aiSettings()
    {
        return $this->renderSettings('ai', 'AI Services Configuration', 'Manage your backend AI processing credentials and provider limits.', 'admin.settings.ai.update');
    }

    public function featuresSettings()
    {
        $this->syncDefaults();
        $settings = AppSetting::where('key', 'like', 'feature_%')->pluck('value', 'key');

        $features = [
            'enhance' => [
                'title' => 'Photo Enhance',
                'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>'
            ],
            'restore' => [
                'title' => 'Restore Old Photo',
                'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'
            ],
            'face' => [
                'title' => 'Face Enhance',
                'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'
            ],
            'upscale' => [
                'title' => 'Upscale to HD',
                'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path></svg>'
            ],
            'colorize' => [
                'title' => 'Colorize Photo',
                'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path></svg>'
            ],
            'background' => [
                'title' => 'Background Fix',
                'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>'
            ],
        ];

        return view('admin.features', compact('settings', 'features'));
    }

    /**
     * Store/Update the settings.
     */
    public function updateSettings(Request $request)
    {
        $inputs = $request->except('_token', '_method');

        // Let's reset known booleans based on request presence
        foreach ($this->defaultSettings as $key => $meta) {
            if ($request->has($key)) {
                AppSetting::where('key', $key)->update(['value' => $request->input($key)]);
            } else if ($meta['type'] == 'boolean') {
                // If it's not present, and it's a known boolean setting, set it to 0
                // We only want to zero it out if it was part of the submitted group implicitly
                // The easiest way is to check if the user is on the specific page. But we are receiving a subset of form fields.
                // A better approach: add a hidden field for the "group" being updated to scope the boolean resets.
            }
        }

        $group = $request->input('setting_group');
        if ($group) {
            // Zero out booleans only for this group if not present
            $groupBooleans = array_keys(array_filter($this->defaultSettings, function ($m) use ($group) {
                return $m['group'] === $group && $m['type'] === 'boolean';
            }));
            foreach ($groupBooleans as $boolKey) {
                if (!$request->has($boolKey)) {
                    AppSetting::where('key', $boolKey)->update(['value' => '0']);
                }
            }
        }

        return redirect()->back()->with('success', 'Configuration updated successfully.');
    }
}
