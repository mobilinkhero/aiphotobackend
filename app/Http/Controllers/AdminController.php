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
        return $this->renderSettings('features', 'App Feature Toggles', 'Instantly hide, show, or pay-wall mobile app features without requiring an app store update.', 'admin.settings.features.update');
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
