<x-app-layout>
    <x-slot name="header">API Logs</x-slot>
    <x-slot name="description">Live request log for all backend API calls made by the mobile app.</x-slot>

    <!-- Force Alpine.js CDN to ensure it works even if local build fails -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <div x-data="{ 
        open: false, 
        selected: { path: '', request: '', response: '', status: '', method: '' },
        logs: @js($logs->values()),
        
        showLog(index) {
            const log = this.logs[index];
            this.selected = {
                path: '/' + log.path,
                request: log.request_body,
                response: log.response_body,
                status: log.status_code,
                method: log.method
            };
            this.open = true;
        },

        formatJson(data) {
            if (!data) return 'No Data';
            try {
                if (typeof data === 'object') return JSON.stringify(data, null, 4);
                return JSON.stringify(JSON.parse(data), null, 4);
            } catch (e) { return data; }
        },

        getAiLogs() {
            if (!this.selected.request) return null;
            try {
                let parsed = (typeof this.selected.request === 'string') ? JSON.parse(this.selected.request) : this.selected.request;
                return parsed._internal_ai_logs || null;
            } catch(e) { return null; }
        }
    }">

        <!-- Stats Row -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-5 mb-8 mt-2">
            <div class="card p-5 flex items-center gap-4">
                <div
                    class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center flex-shrink-0 text-gray-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                        </path>
                    </svg>
                </div>
                <div>
                    <div class="text-2xl font-bold text-gray-900">{{ $total }}</div>
                    <div class="text-sm text-gray-500 uppercase tracking-tighter font-bold">Total</div>
                </div>
            </div>
            <div class="card p-5 flex items-center gap-4">
                <div
                    class="w-10 h-10 rounded-lg bg-green-50 flex items-center justify-center flex-shrink-0 text-green-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <div class="text-2xl font-bold text-gray-900">{{ $success }}</div>
                    <div class="text-sm text-gray-500 uppercase tracking-tighter font-bold text-green-600">Success</div>
                </div>
            </div>
            <div class="card p-5 flex items-center gap-4">
                <div class="w-10 h-10 rounded-lg bg-red-50 flex items-center justify-center flex-shrink-0 text-red-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <div class="text-2xl font-bold text-gray-900">{{ $errors }}</div>
                    <div class="text-sm text-gray-500 uppercase tracking-tighter font-bold text-red-500">Errors</div>
                </div>
            </div>
            <div class="card p-5 flex items-center gap-4">
                <div
                    class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0 text-blue-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <div class="text-2xl font-bold text-gray-900">{{ $avgDuration }}<span
                            class="text-xs ml-1 opacity-50">ms</span></div>
                    <div class="text-sm text-gray-500 uppercase tracking-tighter font-bold text-blue-500">Avg Speed
                    </div>
                </div>
            </div>
        </div>

        <div class="card overflow-hidden border-0 shadow-xl">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                <h3 class="font-bold text-gray-900 text-sm">Real-time Logs</h3>
                <span class="text-[10px] text-blue-500 font-bold bg-blue-50 px-2 py-1 rounded">LIVE FEED</span>
            </div>
            <table class="w-full text-sm">
                <thead>
                    <tr
                        class="bg-white border-b border-gray-100 text-[10px] font-black text-gray-400 uppercase tracking-widest">
                        <th class="text-left px-6 py-4">Method</th>
                        <th class="text-left px-6 py-4">Endpoint</th>
                        <th class="text-left px-6 py-4">Status</th>
                        <th class="text-left px-6 py-4">Time</th>
                        <th class="text-right px-6 py-4">Option</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($logs as $index => $log)
                        <tr class="hover:bg-blue-50/40 transition-all cursor-pointer group" @click="showLog({{ $index }})">
                            <td class="px-6 py-4">
                                <span
                                    class="text-[10px] font-bold font-mono px-2 py-1 rounded bg-gray-100 text-gray-600 border border-gray-200">
                                    {{ $log->method }}
                                </span>
                            </td>
                            <td class="px-6 py-4 font-mono text-[11px] text-gray-600">/{{ $log->path }}</td>
                            <td class="px-6 py-4">
                                @php
                                    $statusLabel = match ((int) $log->status_code) {
                                        200 => 'SUCCESS',
                                        402 => 'OUT OF CREDITS',
                                        422 => 'INPUT ERR',
                                        500 => 'SERVER ERR',
                                        default => 'H' . $log->status_code
                                    };
                                    $statusClass = match ((int) $log->status_code) {
                                        200 => 'bg-green-100 text-green-700 border-green-200',
                                        402 => 'bg-orange-100 text-orange-700 border-orange-200',
                                        default => 'bg-red-100 text-red-700 border-red-200'
                                    };
                                @endphp
                                <span class="px-3 py-1 rounded-full text-[9px] font-black border {{ $statusClass }}">
                                    {{ $statusLabel }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-gray-400 text-[11px]">{{ $log->created_at->diffForHumans() }}</td>
                            <td class="px-6 py-4 text-right">
                                <button type="button" @click.stop="showLog({{ $index }})"
                                    class="bg-blue-600 text-white text-[10px] font-bold px-4 py-2 rounded-lg shadow-md hover:bg-blue-700 active:scale-95 transition-all uppercase tracking-tighter">
                                    Peek Log
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Teleport ensures modal is NOT cut off by parent containers -->
        <template x-teleport="body">
            <div x-show="open"
                class="fixed inset-0 z-[10000] flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-md"
                x-cloak @keydown.escape.window="open = false">

                <div class="bg-white rounded-2xl shadow-[0_35px_60px_-15px_rgba(0,0,0,0.5)] max-w-5xl w-full max-h-[90vh] flex flex-col overflow-hidden border border-gray-100"
                    @click.away="open = false">

                    <div class="px-8 py-6 border-b flex justify-between items-center bg-gray-50/50">
                        <div>
                            <h2 class="text-2xl font-black text-gray-900 tracking-tight" x-text="selected.path"></h2>
                            <div class="flex items-center gap-3 mt-2">
                                <span
                                    class="text-[10px] font-mono px-3 py-1 rounded-md bg-white border border-gray-200 shadow-sm text-gray-500"
                                    x-text="'HTTP ' + selected.status"></span>
                                <span class="text-[10px] font-bold px-3 py-1 rounded-md border" :class="{
                                          'bg-green-100 text-green-700 border-green-300': selected.status == 200,
                                          'bg-orange-100 text-orange-700 border-orange-300': selected.status == 402,
                                          'bg-red-100 text-red-700 border-red-300': selected.status >= 500
                                      }" x-text="{
                                          '200': 'SYSTEMS OPERATIONAL',
                                          '402': 'AI PAYMENT REQUIRED',
                                          '422': 'INVALID REQUEST SENT',
                                          '500': 'SERVER SUBSYSTEM FAILED'
                                      }[selected.status] || 'ERROR DETECTED'"></span>
                            </div>
                        </div>
                        <button @click="open = false"
                            class="bg-gray-100 hover:bg-red-50 hover:text-red-500 text-gray-400 rounded-xl w-12 h-12 flex items-center justify-center text-3xl transition-all duration-200">&times;</button>
                    </div>

                    <div class="p-8 overflow-y-auto space-y-10 bg-white">
                        <div class="relative">
                            <h3
                                class="text-[10px] font-black text-blue-500 mb-4 uppercase tracking-[0.3em] flex items-center gap-2">
                                <span class="p-1 rounded bg-blue-50">App ➔ Server</span>
                            </h3>
                            <pre class="bg-[#0f172a] text-blue-400 p-6 rounded-2xl text-[11px] font-mono shadow-2xl overflow-x-auto border-l-4 border-blue-500/50 leading-relaxed"
                                x-text="formatJson(selected.request)"></pre>
                        </div>

                        <div x-show="getAiLogs()">
                            <h3
                                class="text-[10px] font-black text-orange-500 mb-4 uppercase tracking-[0.3em] flex items-center gap-2">
                                <span class="p-1 rounded bg-orange-50">Server ➔ Replicate AI</span>
                            </h3>
                            <pre class="bg-[#12121e] text-orange-300 p-6 rounded-2xl text-[11px] font-mono shadow-2xl border-l-4 border-orange-500/50 overflow-x-auto leading-relaxed"
                                x-text="formatJson(getAiLogs())"></pre>
                        </div>

                        <div class="relative">
                            <h3
                                class="text-[10px] font-black text-green-500 mb-4 uppercase tracking-[0.3em] flex items-center gap-2">
                                <span class="p-1 rounded bg-green-50">Server ➔ App</span>
                            </h3>
                            <pre class="bg-[#0f172a] text-green-400 p-6 rounded-2xl text-[11px] font-mono shadow-2xl border-l-4 border-green-500/50 overflow-x-auto leading-relaxed"
                                x-text="formatJson(selected.response)"></pre>
                        </div>
                    </div>

                    <div class="px-8 py-5 border-t bg-gray-50 flex justify-end gap-3">
                        <button @click="open = false"
                            class="bg-gray-900 border border-transparent text-white text-[10px] font-black px-8 py-3 rounded-xl shadow-lg hover:shadow-black/20 hover:scale-[1.02] active:scale-95 transition-all uppercase tracking-widest">Dismiss</button>
                    </div>
                </div>
            </div>
        </template>
    </div>
</x-app-layout>