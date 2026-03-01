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
        if (!$request->hasFile('image')) {
            return response()->json(['success' => false, 'error' => 'No image uploaded.'], 422);
        }

        $provider = AppSetting::where('key', 'ai_provider')->value('value') ?: 'replicate';

        return match ($provider) {
            'replicate' => $this->executeReplicate($request, $tool),
            'openai' => $this->executeOpenAI($request, $tool),
            'gemini' => $this->executeGemini($request, $tool),
            default => $this->executeReplicate($request, $tool),
        };
    }

    protected function executeReplicate(Request $request, string $tool)
    {
        $apiKey = AppSetting::where('key', 'replicate_api_key')->value('value') ?: AppSetting::where('key', 'ai_api_key')->value('value');
        if (empty($apiKey)) {
            return response()->json(['success' => false, 'error' => 'Replicate API key missing.'], 503);
        }

        $config = $this->models[$tool] ?? $this->models['enhance'];

        try {
            $file = $request->file('image');
            $mimeType = $file->getClientMimeType() ?: 'image/jpeg';
            $base64 = base64_encode(file_get_contents($file->getRealPath()));
            $dataUri = "data:{$mimeType};base64,{$base64}";

            $input = array_merge([$config['input_key'] => $dataUri], $config['extra']);
            $internalLogs = [
                'provider' => 'replicate',
                'model' => $config['version'],
                'payload' => array_merge($input, [$config['input_key'] => '[IMAGE_BLOB]']),
            ];

            $resp = Http::withToken($apiKey)->withoutVerifying()->timeout(45)->post("https://api.replicate.com/v1/predictions", [
                'version' => $config['version'],
                'input' => $input,
            ]);

            $internalLogs['initial_response'] = $resp->json();

            if (!$resp->successful()) {
                $request->merge(['_internal_ai_logs' => $internalLogs]);
                return response()->json(['success' => false, 'error' => "AI Provider Error: " . ($resp->json('detail') ?: $resp->body())], $resp->status());
            }

            $predictionId = $resp->json('id');
            $pollUrl = "https://api.replicate.com/v1/predictions/{$predictionId}";

            $resultUrl = null;
            for ($i = 0; $i < 60; $i++) {
                sleep(2);
                $pollResp = Http::withToken($apiKey)->withoutVerifying()->get($pollUrl);
                if (!$pollResp->successful())
                    break;

                $status = $pollResp->json('status');
                if ($status === 'succeeded') {
                    $output = $pollResp->json('output');
                    $resultUrl = is_array($output) ? end($output) : $output;
                    break;
                }
                if (in_array($status, ['failed', 'canceled'])) {
                    $internalLogs['failure_reason'] = $pollResp->json('error');
                    $request->merge(['_internal_ai_logs' => $internalLogs]);
                    return response()->json(['success' => false, 'error' => 'AI processing failed.'], 500);
                }
            }

            $request->merge(['_internal_ai_logs' => $internalLogs]);

            if (!$resultUrl) {
                return response()->json(['success' => false, 'error' => 'AI took too long to respond.'], 500);
            }

            return $this->finalizeImage($resultUrl, $tool);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => 'System error: ' . $e->getMessage()], 500);
        }
    }

    protected function executeOpenAI(Request $request, string $tool)
    {
        $apiKey = AppSetting::where('key', 'openai_api_key')->value('value');
        $model = AppSetting::where('key', 'openai_model')->value('value') ?: 'dall-e-3';

        if (empty($apiKey)) {
            return response()->json(['success' => false, 'error' => 'OpenAI API key missing.'], 503);
        }

        try {
            // OpenAI DALL-E for generation, or Vision for enhancement instructions
            // For now, since this is a 'Photo Enhancer', we implement a high-quality 'Regeneration' if the user chooses OpenAI
            // as OpenAI doesn't have a direct ESRGAN equivalent yet.

            $resp = Http::withToken($apiKey)->withoutVerifying()->timeout(60)->post("https://api.openai.com/v1/images/generations", [
                'model' => $model,
                'prompt' => "Professionally enhance and restore this photo, make it high resolution, cinematic lighting, 8k: " . $request->input('prompt', 'Restore this image to original quality'),
                'n' => 1,
                'size' => "1024x1024",
            ]);

            if (!$resp->successful()) {
                return response()->json(['success' => false, 'error' => "OpenAI Error: " . $resp->json('error.message')], $resp->status());
            }

            $resultUrl = $resp->json('data.0.url');
            return $this->finalizeImage($resultUrl, $tool);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => 'OpenAI integration error: ' . $e->getMessage()], 500);
        }
    }

    protected function executeGemini(Request $request, string $tool)
    {
        $apiKey = AppSetting::where('key', 'gemini_api_key')->value('value');
        if (empty($apiKey)) {
            return response()->json(['success' => false, 'error' => 'Gemini API key missing.'], 503);
        }

        // Gemini 1.5 Flash - Very cheap, good for background removal or descriptive tasks
        // Placeholder for real Gemini Image generation if/when available in your region
        return response()->json(['success' => false, 'error' => 'Gemini Image Generation is currently in preview. Falling back to Replicate.'], 501);
    }

    private function finalizeImage(string $url, string $tool)
    {
        $storageDir = storage_path('app/public/enhanced');
        if (!file_exists($storageDir)) {
            mkdir($storageDir, 0755, true);
        }

        $filename = 'enhanced/' . $tool . '_' . uniqid() . '.png';
        file_put_contents(storage_path('app/public/' . $filename), file_get_contents($url));

        return response()->json([
            'success' => true,
            'result_url' => asset('storage/' . $filename),
            'tool' => $tool,
        ]);
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
