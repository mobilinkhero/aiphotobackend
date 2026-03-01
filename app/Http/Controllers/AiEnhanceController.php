<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use App\Models\AppSetting;

class AiEnhanceController extends Controller
{
    /**
     * Tool Mapping with locked version hashes.
     */
    private array $models = [
        'enhance' => [
            'version' => '7de2ea26c616d5bf2245ad0d5e24f0ff9a6204578a5c876db53142edd9d2cd56', // Codeformer
            'input_key' => 'image',
            'extra' => ['codeformer_fidelity' => 0.7, 'upscale' => 2],
        ],
        'colorize' => [
            'version' => 'ca494ba129e44e45f661d6ece83c4c98a9a7c774309beca01429b58fce8aa695', // DDColor
            'input_key' => 'image',
            'extra' => ['model_size' => 'large'],
        ],
        'restore' => [
            'version' => '7de2ea26c616d5bf2245ad0d5e24f0ff9a6204578a5c876db53142edd9d2cd56', // Codeformer
            'input_key' => 'image',
            'extra' => ['codeformer_fidelity' => 0.3, 'upscale' => 2],
        ],
        'face' => [
            'version' => '297a243c5b9678170c242b3ac2bc657074d08a5435edb1446337ba85a0694e9f', // GFPGAN
            'input_key' => 'img',
            'extra' => ['upscale' => 2],
        ],
        'upscale' => [
            'version' => 'a01b0512004918ca55d02e554914a9eca63909fa83a29ff0f115c78a7045574f', // Swin2SR
            'input_key' => 'image',
            'extra' => [],
        ],
        'background' => [
            'version' => '95fcc2a26d3899cd6c2691c900465aaeff466285a65c14638cc5f36f34befaf1', // Remove-BG
            'input_key' => 'image',
            'extra' => [],
        ],
    ];

    // ───────────────────────────────────────────────────
    //  Public Tool Endpoints
    // ───────────────────────────────────────────────────

    public function processEnhance(Request $request)
    {
        return $this->executeProcessing($request, 'enhance');
    }
    public function processColorize(Request $request)
    {
        return $this->executeProcessing($request, 'colorize');
    }
    public function processRestore(Request $request)
    {
        return $this->executeProcessing($request, 'restore');
    }
    public function processFace(Request $request)
    {
        return $this->executeProcessing($request, 'face');
    }
    public function processUpscale(Request $request)
    {
        return $this->executeProcessing($request, 'upscale');
    }
    public function processBackground(Request $request)
    {
        return $this->executeProcessing($request, 'background');
    }

    /**
     * Core processing logic used by all specific endpoints.
     */
    protected function executeProcessing(Request $request, string $tool)
    {
        // Avoid Laravel's strict native 'image' or 'file' checking
        // as the server is missing the php ext-fileinfo extension.
        if (!$request->hasFile('image')) {
            return response()->json(['success' => false, 'error' => 'No image uploaded. (Missing image field)'], 422);
        }

        $apiKey = AppSetting::where('key', 'ai_api_key')->value('value');
        if (empty($apiKey)) {
            return response()->json(['success' => false, 'error' => 'API key missing.'], 503);
        }

        $config = $this->models[$tool] ?? $this->models['enhance'];

        try {
            $file = $request->file('image');

            // Bypass strict mime-type check that depends on ext-fileinfo
            $mimeType = $file->getClientMimeType() ?: 'image/jpeg';
            if (!str_starts_with($mimeType, 'image/')) {
                return response()->json(['success' => false, 'error' => 'The uploaded file must be an image.'], 422);
            }
            $base64 = base64_encode(file_get_contents($file->getRealPath()));
            $dataUri = "data:{$mimeType};base64,{$base64}";

            // 1. Send to Replicate
            $input = array_merge([$config['input_key'] => $dataUri], $config['extra']);
            $resp = Http::withToken($apiKey)->withoutVerifying()->timeout(30)->post("https://api.replicate.com/v1/predictions", [
                'version' => $config['version'],
                'input' => $input,
            ]);

            if (!$resp->successful()) {
                return response()->json(['success' => false, 'error' => "Replicate Start Error: " . $resp->body()], $resp->status());
            }

            $predictionId = $resp->json('id');
            $pollUrl = "https://api.replicate.com/v1/predictions/{$predictionId}";

            // 2. Poll Status (max 120s)
            $resultUrl = null;
            for ($i = 0; $i < 60; $i++) {
                sleep(2);
                $pollResp = Http::withToken($apiKey)->withoutVerifying()->get($pollUrl);

                if (!$pollResp->successful()) {
                    return response()->json(['success' => false, 'error' => 'Lost connection during polling.'], 500);
                }

                $status = $pollResp->json('status');

                if ($status === 'succeeded') {
                    $output = $pollResp->json('output');
                    $resultUrl = is_array($output) ? end($output) : $output;
                    break;
                }

                if (in_array($status, ['failed', 'canceled'])) {
                    return response()->json(['success' => false, 'error' => 'AI processing failed on server.'], 500);
                }
            }

            if (!$resultUrl) {
                return response()->json(['success' => false, 'error' => 'Processing timeout.'], 500);
            }

            // 3. Save locally
            Storage::disk('public')->makeDirectory('enhanced');
            $filename = 'enhanced/' . $tool . '_' . uniqid() . '.png';
            Storage::disk('public')->put($filename, file_get_contents($resultUrl));

            return response()->json([
                'success' => true,
                'result_url' => asset('storage/' . $filename),
                'tool' => $tool,
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => 'System error: ' . $e->getMessage()], 500);
        }
    }

    // ───────────────────────────────────────────────────
    //  Admin Test Lab Logic (Compatible with new mapping)
    // ───────────────────────────────────────────────────

    public function testPage()
    {
        $apiKey = AppSetting::where('key', 'ai_api_key')->value('value');
        $tools = array_keys($this->models);
        return view('admin.ai_test', compact('apiKey', 'tools'));
    }

    public function testRun(Request $request)
    {
        $tool = $request->input('tool', 'enhance');
        $result = $this->executeProcessing($request, $tool);
        $data = json_decode($result->getContent(), true);

        return view('admin.ai_test', [
            'apiKey' => AppSetting::where('key', 'ai_api_key')->value('value'),
            'tools' => array_keys($this->models),
            'resultUrl' => $data['success'] ? ($data['result_url'] ?? null) : null,
            'error' => $data['error'] ?? null
        ]);
    }
}
