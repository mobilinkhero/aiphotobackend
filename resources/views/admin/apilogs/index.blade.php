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

    <!-- Table wrapped in Alpine.js for Modals -->
    <div x-data="{ 
        open: false, 
        selectedLog: { path: '', request: '', response: '', status: '' } 
    }">
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
                        <tr class="hover:bg-gray-100 transition-colors cursor-pointer" @click="
                                    selectedLog = { 
                                        path: '/{{ $log->path }}', 
                                        request: {!! json_encode($log->request_body) !!}, 
                                        response: {!! json_encode($log->response_body) !!},
                                        status: '{{ $log->status_code }}'
                                    };
                                    open = true;
                                ">
                            <td class="px-6 py-3">
                                <span
                                    class="px-2 py-0.5 rounded text-xs font-bold font-mono
                                            {{ $log->method === 'GET' ? 'bg-blue-50 text-blue-700' : 'bg-purple-50 text-purple-700' }}">
                                    {{ $log->method }}
                                </span>
                            </td>
                            <td class="px-6 py-3 font-mono text-xs text-gray-700">/{{ $log->path }}</td>
                            <td class="px-6 py-3">
                                <span
                                    class="px-2 py-0.5 rounded text-xs font-semibold font-mono
                                            {{ $log->status_code === 200 ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700' }}">
                                    {{ $log->status_code }}
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

        <!-- Details Modal -->
        <div x-show="open" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50" x-cloak
            @keydown.escape.window="open = false">

            <div class="bg-white rounded-xl shadow-2xl max-w-4xl w-full max-h-[90vh] flex flex-col overflow-hidden"
                @click.away="open = false">

                <div class="px-6 py-4 border-b flex justify-between items-center bg-gray-50">
                    <div>
                        <h2 class="text-lg font-bold text-gray-900" x-text="selectedLog.path"></h2>
                        <span class="text-xs font-mono text-gray-500" x-text="'Status: ' + selectedLog.status"></span>
                    </div>
                    <button @click="open = false"
                        class="text-gray-400 hover:text-gray-600 font-bold text-2xl">&times;</button>
                </div>

                <div class="p-6 overflow-y-auto space-y-6">
                    <div>
                        <h3
                            class="text-sm font-semibold text-gray-900 mb-2 uppercase tracking-wider flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                            App Request Payload (App → Server)
                        </h3>
                        <pre class="bg-gray-900 text-green-400 p-4 rounded-lg text-xs font-mono overflow-x-auto"
                            x-text="selectedLog.request ? JSON.stringify(JSON.parse(selectedLog.request), null, 4) : 'No Data'"></pre>
                    </div>

                    <div>
                        <h3
                            class="text-sm font-semibold text-gray-900 mb-2 uppercase tracking-wider flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-green-500"></span>
                            Server Response Payload (Server → App)
                        </h3>
                        <pre class="bg-gray-900 text-blue-300 p-4 rounded-lg text-xs font-mono overflow-x-auto"
                            x-text="selectedLog.response ? (selectedLog.response.startsWith('{') ? JSON.stringify(JSON.parse(selectedLog.response), null, 4) : selectedLog.response) : 'No Data'"></pre>
                    </div>
                </div>

                <div class="px-6 py-3 border-t bg-gray-50 text-right">
                    <button @click="open = false" class="btn btn-secondary text-sm">Close Inspector</button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>