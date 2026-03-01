<x-app-layout>
    <x-slot name="header">API Logs</x-slot>
    <x-slot name="description">Live request log for all backend API calls made by the mobile app.</x-slot>

    <!-- Stats Row -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-5 mb-8 mt-2">
        <div class="card p-5 flex items-center gap-4">
            <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                    </path>
                </svg>
            </div>
            <div>
                <div class="text-2xl font-bold text-gray-900">{{ $total }}</div>
                <div class="text-sm text-gray-500">Total Requests</div>
            </div>
        </div>
        <div class="card p-5 flex items-center gap-4">
            <div class="w-10 h-10 rounded-lg bg-green-50 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div>
                <div class="text-2xl font-bold text-gray-900">{{ $success }}</div>
                <div class="text-sm text-gray-500">Successful (2xx)</div>
            </div>
        </div>
        <div class="card p-5 flex items-center gap-4">
            <div class="w-10 h-10 rounded-lg bg-red-50 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div>
                <div class="text-2xl font-bold text-gray-900">{{ $errors }}</div>
                <div class="text-sm text-gray-500">Errors (4xx/5xx)</div>
            </div>
        </div>
        <div class="card p-5 flex items-center gap-4">
            <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div>
                <div class="text-2xl font-bold text-gray-900">{{ $avgDuration }}<span
                        class="text-base font-normal text-gray-400">ms</span></div>
                <div class="text-sm text-gray-500">Avg Duration</div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('logModal', {
                open: false,
                selectedLog: { path: '', request: '', response: '', status: '' },

                // Helper to safely format JSON or return raw text
                formatData(data) {
                    if (!data) return 'No Data';
                    try {
                        // If it's already an object, just stringify it
                        if (typeof data === 'object') return JSON.stringify(data, null, 4);
                        // If it's a string, try parsing it first
                        return JSON.stringify(JSON.parse(data), null, 4);
                    } catch (e) {
                        return data; // Return raw text if not JSON
                    }
                },

                // Helper specifically for internal logs inside request
                getInternalLogs() {
                    let req = this.selectedLog.request;
                    if (!req) return null;
                    try {
                        let parsed = (typeof req === 'string') ? JSON.parse(req) : req;
                        return parsed._internal_ai_logs || null;
                    } catch (e) { return null; }
                },

                show(path, req, res, status) {
                    console.log('Opening Log:', path); // Debug log
                    this.selectedLog = { path, request: req, response: res, status };
                    this.open = true;
                }
            })
        })
    </script>

    <div class="card overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-900 text-sm">Recent API Requests</h3>
        </div>
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-100">
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        Method
                    </th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        Endpoint</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        Status
                    </th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        Duration</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">IP
                    </th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        Time
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach ($logs as $log)
                    <tr class="hover:bg-gray-100 transition-colors cursor-pointer"
                        @click="$store.logModal.show('/{{ $log->path }}', @js($log->request_body), @js($log->response_body), '{{ $log->status_code }}')">
                        <td class="px-6 py-3">
                            <span
                                class="px-2 py-0.5 rounded text-xs font-bold font-mono
                                                            {{ $log->method === 'GET' ? 'bg-blue-50 text-blue-700' : 'bg-purple-50 text-purple-700' }}">
                                {{ $log->method }}
                            </span>
                        </td>
                        <td class="px-6 py-3 font-mono text-xs text-gray-700">/{{ $log->path }}</td>
                        <td class="px-6 py-3">
                            @php
                                $statusLabel = match ((int) $log->status_code) {
                                    200 => 'SUCCESS',
                                    402 => 'AI OUT OF CREDITS',
                                    422 => 'INPUT ERROR',
                                    500 => 'SERVER CRASH',
                                    503 => 'CONFIG MISSING',
                                    default => 'UNKNOWN (' . $log->status_code . ')'
                                };
                                $statusClass = match ((int) $log->status_code) {
                                    200 => 'bg-green-100 text-green-700 border-green-200',
                                    402 => 'bg-orange-100 text-orange-700 border-orange-200',
                                    422 => 'bg-amber-100 text-amber-700 border-amber-200',
                                    500, 503 => 'bg-red-100 text-red-700 border-red-200',
                                    default => 'bg-gray-100 text-gray-700 border-gray-200'
                                };
                            @endphp
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold border {{ $statusClass }}">
                                {{ $statusLabel }}
                            </span>
                        </td>
                        <td class="px-6 py-3 text-gray-600 font-mono text-xs">{{ round($log->duration * 1000) }}ms</td>
                        <td class="px-6 py-3 text-gray-400 font-mono text-xs">{{ $log->ip }}</td>
                        <td class="px-6 py-3 text-gray-400 text-xs">{{ $log->created_at->diffForHumans() }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @push('modals')
        <!-- Details Modal (Teleported to root to fix sidebar layout mix) -->
        <div x-data x-show="$store.logModal.open"
            class="fixed inset-0 z-[999] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm" x-cloak
            @keydown.escape.window="$store.logModal.open = false">

            <div class="bg-white rounded-xl shadow-2xl max-w-4xl w-full max-h-[90vh] flex flex-col overflow-hidden border border-gray-200"
                @click.away="$store.logModal.open = false">

                <div class="px-6 py-4 border-b flex justify-between items-center bg-gray-50/80 backdrop-blur">
                    <div>
                        <h2 class="text-lg font-bold text-gray-900" x-text="$store.logModal.selectedLog.path"></h2>
                        <div class="flex items-center gap-2 mt-1">
                            <span
                                class="text-xs font-mono px-2 py-0.5 rounded bg-gray-100 text-gray-600 border border-gray-200"
                                x-text="'HTTP ' + $store.logModal.selectedLog.status"></span>
                            <span class="text-xs font-bold px-2 py-0.5 rounded" :class="{
                                          'bg-green-100 text-green-700': $store.logModal.selectedLog.status == 200,
                                          'bg-orange-100 text-orange-700': $store.logModal.selectedLog.status == 402,
                                          'bg-amber-100 text-amber-700': $store.logModal.selectedLog.status == 422,
                                          'bg-red-100 text-red-700': $store.logModal.selectedLog.status >= 500
                                      }" x-text="{
                                          '200': 'ALL SYSTEMS OK',
                                          '402': 'AI BALANCE REQUIRED',
                                          '422': 'BAD IMAGE/REQUEST',
                                          '500': 'SERVER INTERNAL ERROR',
                                          '503': 'SYSTEM CONFIG ERROR'
                                      }[$store.logModal.selectedLog.status] || 'UNEXPECTED ERROR'"></span>
                        </div>
                    </div>
                    <button @click="$store.logModal.open = false"
                        class="text-gray-400 hover:text-gray-900 transition-colors p-2">&times;</button>
                </div>

                <div class="p-6 overflow-y-auto space-y-6">
                    <div>
                        <h3
                            class="text-sm font-semibold text-gray-900 mb-2 uppercase tracking-wider flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                            App Request Payload (App → Server)
                        </h3>
                        <pre class="bg-gray-900 text-green-400 p-4 rounded-lg text-xs font-mono overflow-x-auto whitespace-pre-wrap"
                            x-text="$store.logModal.formatData($store.logModal.selectedLog.request)"></pre>
                    </div>

                    <div x-show="$store.logModal.getInternalLogs()">
                        <h3
                            class="text-sm font-semibold text-gray-900 mb-2 uppercase tracking-wider flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-orange-500 border border-white"></span>
                            Internal AI Workflow (Server ↔ Replicate)
                        </h3>
                        <pre class="bg-[#1e1e2e] text-orange-200 p-4 rounded-lg text-xs font-mono overflow-x-auto border border-orange-500/20 whitespace-pre-wrap"
                            x-text="$store.logModal.formatData($store.logModal.getInternalLogs())"></pre>
                    </div>

                    <div>
                        <h3
                            class="text-sm font-semibold text-gray-900 mb-2 uppercase tracking-wider flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-green-500"></span>
                            Server Response Payload (Server → App)
                        </h3>
                        <pre class="bg-gray-900 text-blue-300 p-4 rounded-lg text-xs font-mono overflow-x-auto whitespace-pre-wrap"
                            x-text="$store.logModal.formatData($store.logModal.selectedLog.response)"></pre>
                    </div>
                </div>

                <div class="px-6 py-3 border-t bg-gray-50 text-right">
                    <button @click="$store.logModal.open = false"
                        class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">Close
                        Inspector</button>
                </div>
            </div>
        </div>
    @endpush
</x-app-layout>