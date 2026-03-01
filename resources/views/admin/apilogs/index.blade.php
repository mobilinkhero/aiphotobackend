<x-app-layout>
    <x-slot name="header">API Logs</x-slot>
    <x-slot name="description">Live request log for all backend API calls made by the mobile app.</x-slot>

    <!-- Force Alpine.js CDN -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <div x-data="{ 
        open: false, 
        loading: false,
        selected: { path: '', request: '', response: '', status: '', method: '' },
        
        async showLog(id) {
            this.loading = true;
            this.open = true;
            this.selected = { path: 'Loading...', request: '', response: '', status: 0, method: '' };
            
            try {
                const response = await fetch(`/admin/api-logs/${id}`);
                const log = await response.json();
                
                this.selected = {
                    path: '/' + (log.path || ''),
                    request: log.request_body,
                    response: log.response_body,
                    status: log.status_code,
                    method: log.method
                };
            } catch (e) {
                this.selected.path = 'Error loading log';
            } finally {
                this.loading = false;
            }
        },

        formatJson(data) {
            if (!data) return 'No Data';
            if (typeof data === 'object') return JSON.stringify(data, null, 4);
            try {
                // Remove potential data URIs/huge blobs from preview if needed,
                // but here we want to see the full log.
                return JSON.stringify(JSON.parse(data), null, 4);
            } catch (e) { return data; }
        },

        getAiLogs() {
            if (!this.selected.request) return null;
            try {
                let req = this.selected.request;
                let parsed = (typeof req === 'string') ? JSON.parse(req) : req;
                return parsed._internal_ai_logs || null;
            } catch(e) { return null; }
        }
    }" class="relative">

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
                    <div class="text-[10px] text-gray-500 uppercase tracking-widest font-bold">Total</div>
                </div>
            </div>
            <div class="card p-5 flex items-center gap-4 border-l-4 border-l-green-500">
                <div
                    class="w-10 h-10 rounded-lg bg-green-50 flex items-center justify-center flex-shrink-0 text-green-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <div class="text-2xl font-bold text-gray-900">{{ $success }}</div>
                    <div class="text-[10px] text-green-600 uppercase tracking-widest font-bold">Success</div>
                </div>
            </div>
            <div class="card p-5 flex items-center gap-4 border-l-4 border-l-red-500">
                <div class="w-10 h-10 rounded-lg bg-red-50 flex items-center justify-center flex-shrink-0 text-red-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <div class="text-2xl font-bold text-gray-900">{{ $errors }}</div>
                    <div class="text-[10px] text-red-500 uppercase tracking-widest font-bold">Errors</div>
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
                    <div class="text-2xl font-bold text-gray-900">{{ $avgDuration }}ms</div>
                    <div class="text-[10px] text-blue-500 uppercase tracking-widest font-bold">Avg Speed</div>
                </div>
            </div>
        </div>

        <div class="card overflow-hidden shadow-sm">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                <h3 class="font-bold text-gray-900 text-sm italic">Real-time Logs</h3>
                <span
                    class="text-[10px] text-blue-600 font-bold bg-blue-50 px-2 py-1 rounded border border-blue-100 uppercase tracking-widest">Live
                    Feed</span>
            </div>
            <table class="w-full text-sm">
                <thead>
                    <tr
                        class="bg-white border-b border-gray-100 text-[10px] font-black text-gray-400 uppercase tracking-widest">
                        <th class="text-left px-6 py-4">Method</th>
                        <th class="text-left px-6 py-4">Endpoint</th>
                        <th class="text-left px-6 py-4">Status</th>
                        <th class="text-left px-6 py-4">Time</th>
                        <th class="text-right px-6 py-4">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($logs as $log)
                        <tr class="hover:bg-blue-50/50 transition-all cursor-pointer group"
                            @click="showLog({{ $log->id }})">
                            <td class="px-6 py-4">
                                <span
                                    class="text-[10px] font-bold font-mono px-2 py-1 rounded bg-gray-100 text-gray-500 border border-gray-200">
                                    {{ $log->method }}
                                </span>
                            </td>
                            <td class="px-6 py-4 font-mono text-[11px] text-gray-700">/{{ $log->path }}</td>
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
                                <button type="button"
                                    class="bg-blue-600 text-white text-[10px] font-bold px-4 py-2 rounded-lg shadow hover:bg-blue-700 transition-all uppercase">
                                    Peek Log
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Teleport ensures modal is NOT cut off and has z-index priority -->
        <template x-teleport="body">
            <div x-show="open"
                class="fixed inset-0 z-[99999] flex items-center justify-center p-4 bg-gray-900/80 backdrop-blur-md"
                x-cloak @keydown.escape.window="open = false">

                <div class="bg-white rounded-3xl shadow-[0_35px_100px_-15px_rgba(0,0,0,0.8)] max-w-5xl w-full max-h-[90vh] flex flex-col overflow-hidden border border-gray-100"
                    @click.away="open = false">

                    <div class="px-8 py-6 border-b flex justify-between items-center bg-gray-50/50 backdrop-blur-sm">
                        <div class="flex-1">
                            <h2 class="text-2xl font-black text-gray-900 tracking-tight" x-text="selected.path"></h2>
                            <div class="flex items-center gap-3 mt-2">
                                <span
                                    class="text-[10px] font-mono px-3 py-1 rounded-md bg-white border border-gray-200 shadow-sm text-gray-500"
                                    x-text="'HTTP ' + selected.status"></span>
                                <span class="text-[10px] font-black px-3 py-1 rounded-md border" :class="{
                                          'bg-green-100 text-green-700 border-green-300': selected.status == 200,
                                          'bg-orange-100 text-orange-700 border-orange-300': selected.status == 402,
                                          'bg-red-100 text-red-700 border-red-300': selected.status >= 500
                                      }" x-text="{
                                          '200': 'SYSTEM OK',
                                          '402': 'PAYMENT REQUIRED (REPLICATE)',
                                          '422': 'INVALID REQUEST BODY',
                                          '500': 'SERVER SUBSYSTEM CRASHED'
                                      }[selected.status] || 'ERROR ALERT'"></span>
                            </div>
                        </div>
                        <button @click="open = false"
                            class="bg-white hover:bg-red-50 hover:text-red-500 text-gray-400 rounded-full w-12 h-12 flex items-center justify-center text-3xl font-light shadow-sm transition-all">&times;</button>
                    </div>

                    <div class="p-8 overflow-y-auto space-y-10 bg-white min-h-[400px]">
                        <!-- Loading State -->
                        <template x-if="loading">
                            <div class="flex flex-col items-center justify-center py-20 animate-pulse">
                                <div
                                    class="w-12 h-12 border-4 border-blue-500 border-t-transparent rounded-full animate-spin mb-4">
                                </div>
                                <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Fetching full
                                    log content...</span>
                            </div>
                        </template>

                        <div x-show="!loading">
                            <!-- Payload Blocks -->
                            <div class="space-y-10">
                                <div>
                                    <h3
                                        class="text-[10px] font-black text-blue-600 mb-4 uppercase tracking-[0.4em] flex items-center gap-3">
                                        <span
                                            class="w-2 h-2 rounded-full bg-blue-600 shadow-lg shadow-blue-500/50"></span>
                                        Input Data (Mobile App ➔ Server)
                                    </h3>
                                    <pre class="bg-[#0b0e14] text-gray-300 p-8 rounded-2xl text-[11px] font-mono shadow-2xl overflow-x-auto border border-white/5 leading-relaxed"
                                        x-text="formatJson(selected.request)"></pre>
                                </div>

                                <div x-show="getAiLogs()">
                                    <h3
                                        class="text-[10px] font-black text-orange-600 mb-4 uppercase tracking-[0.4em] flex items-center gap-3">
                                        <span
                                            class="w-2 h-2 rounded-full bg-orange-600 shadow-lg shadow-orange-500/50"></span>
                                        Work Detail (Server ↔ Replicate)
                                    </h3>
                                    <pre class="bg-[#0f1118] text-orange-200 p-8 rounded-2xl text-[11px] font-mono shadow-2xl border border-orange-500/10 overflow-x-auto leading-relaxed"
                                        x-text="formatJson(getAiLogs())"></pre>
                                </div>

                                <div>
                                    <h3
                                        class="text-[10px] font-black text-green-600 mb-4 uppercase tracking-[0.4em] flex items-center gap-3">
                                        <span
                                            class="w-2 h-2 rounded-full bg-green-600 shadow-lg shadow-green-500/50"></span>
                                        Final Result (Server ➔ Mobile App)
                                    </h3>
                                    <pre class="bg-[#0b0e14] text-blue-200 p-8 rounded-2xl text-[11px] font-mono shadow-2xl border border-white/5 overflow-x-auto leading-relaxed"
                                        x-text="formatJson(selected.response)"></pre>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="px-8 py-6 border-t bg-gray-50 flex justify-end">
                        <button @click="open = false"
                            class="bg-gray-900 text-white text-xs font-black px-12 py-4 rounded-2xl shadow-xl hover:scale-[1.02] active:scale-95 transition-all uppercase tracking-widest">Close
                            Dashboard</button>
                    </div>
                </div>
            </div>
        </template>
    </div>
</x-app-layout>