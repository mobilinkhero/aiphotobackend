<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.apilogs') }}"
                class="p-2 hover:bg-gray-100 rounded-lg transition-colors text-gray-400 hover:text-gray-900">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <span>Inspect API Request</span>
        </div>
    </x-slot>
    <x-slot name="description">Comprehensive technical breakdown of the selected mobile app interaction.</x-slot>

    <div class="max-w-6xl mx-auto py-4 space-y-8">
        <!-- Overview Header -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-8 py-6 bg-gray-50/50 flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <span
                        class="px-3 py-1 bg-gray-900 text-white font-mono text-xs font-bold rounded-md tracking-widest uppercase">
                        {{ $log->method }}
                    </span>
                    <h2 class="text-xl font-black text-gray-900 tracking-tight">/{{ $log->path }}</h2>
                </div>
                <div class="flex items-center gap-3">
                    @php
                        $statusClass = match ((int) $log->status_code) {
                            200 => 'bg-green-100 text-green-700 border-green-200',
                            402 => 'bg-orange-100 text-orange-700 border-orange-200',
                            422 => 'bg-amber-100 text-amber-700 border-amber-200',
                            default => 'bg-red-100 text-red-700 border-red-200'
                        };
                    @endphp
                    <span
                        class="px-3 py-1 rounded-md border font-black text-[10px] tracking-[0.1em] uppercase {{ $statusClass }}">
                        HTTP {{ $log->status_code }} - {{ match ((int) $log->status_code) {
    200 => 'SUCCESS',
    402 => 'BALANCE REQUIRED',
    422 => 'IMAGE INPUT REFUSED',
    500 => 'SYSTEM CRASHED',
    default => 'UNKNOWN ERROR'
} }}
                    </span>
                    <span
                        class="text-xs text-gray-400 font-medium bg-white px-3 py-1 rounded-md border">{{ $log->created_at->format('M j, Y - H:i:s') }}</span>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 divide-y md:divide-y-0 md:divide-x border-t">
                <div class="p-6">
                    <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Execution Speed
                    </div>
                    <div class="text-lg font-bold text-gray-900 font-mono">{{ round($log->duration * 1000) }}ms <span
                            class="text-[10px] opacity-10 font-sans">({{ $log->duration }}s)</span></div>
                </div>
                <div class="p-6">
                    <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Client IP</div>
                    <div class="text-lg font-bold text-gray-900 font-mono">{{ $log->ip }}</div>
                </div>
                <div class="p-6">
                    <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Request Age</div>
                    <div class="text-lg font-bold text-gray-900">{{ $log->created_at->diffForHumans() }}</div>
                </div>
            </div>
        </div>

        <!-- Request / Response Data -->
        <div class="space-y-10">
            <!-- App to Server -->
            <div class="relative">
                <div class="flex items-center gap-3 mb-4">
                    <div
                        class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold text-xs ring-4 ring-blue-50">
                        1</div>
                    <h3 class="text-xs font-black text-gray-400 uppercase tracking-[0.3em]">Payload From Mobile App</h3>
                </div>
                <div class="group relative">
                    <pre
                        class="bg-[#0f172a] text-blue-400 p-8 rounded-3xl text-[11px] font-mono shadow-2xl overflow-x-auto border-l-[6px] border-blue-500/80 leading-relaxed max-h-[800px]">@json(json_decode($log->request_body), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)</pre>
                </div>
            </div>

            <!-- Internal AI Logs (If any) -->
            @php
                $requestBody = json_decode($log->request_body, true);
                $internalLogs = $requestBody['_internal_ai_logs'] ?? null;
            @endphp
            @if($internalLogs)
                <div class="relative">
                    <div class="flex items-center gap-3 mb-4">
                        <div
                            class="w-8 h-8 rounded-full bg-orange-500 flex items-center justify-center text-white font-bold text-xs ring-4 ring-orange-50">
                            2</div>
                        <h3 class="text-xs font-black text-gray-400 uppercase tracking-[0.3em]">AI Sub-Workflow (Replicate)
                        </h3>
                    </div>
                    <div class="group relative">
                        <pre
                            class="bg-[#12121e] text-orange-200 p-8 rounded-3xl text-[11px] font-mono shadow-2xl border-l-[6px] border-orange-500/80 overflow-x-auto leading-relaxed max-h-[800px]">@json($internalLogs, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)</pre>
                    </div>
                </div>
            @endif

            <!-- Server to App -->
            <div class="relative">
                <div class="flex items-center gap-3 mb-4">
                    <div
                        class="w-8 h-8 rounded-full bg-green-500 flex items-center justify-center text-white font-bold text-xs ring-4 ring-green-50">
                        {{ $internalLogs ? '3' : '2' }}</div>
                    <h3 class="text-xs font-black text-gray-400 uppercase tracking-[0.3em]">Final Server Response</h3>
                </div>
                <div class="group relative">
                    @php
                        $res = json_decode($log->response_body);
                    @endphp
                    <pre
                        class="bg-[#0f172a] text-green-400 p-8 rounded-3xl text-[11px] font-mono shadow-2xl border-l-[6px] border-green-500/80 overflow-x-auto leading-relaxed max-h-[800px]">@json($res, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)</pre>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>