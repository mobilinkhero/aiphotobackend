<x-app-layout>
    <x-slot name="header">API Logs</x-slot>
    <x-slot name="description">Live request log for all backend API calls made by the mobile app.</x-slot>

    <!-- Stats Row -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-5 mb-8 mt-2">
        <div class="card p-5 flex items-center gap-4">
            <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center flex-shrink-0 text-gray-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
            </div>
            <div>
                <div class="text-2xl font-bold text-gray-900">{{ $total }}</div>
                <div class="text-[10px] text-gray-500 uppercase tracking-widest font-bold">Total</div>
            </div>
        </div>
        <div class="card p-5 flex items-center gap-4 border-l-4 border-l-green-500">
            <div class="w-10 h-10 rounded-lg bg-green-50 flex items-center justify-center flex-shrink-0 text-green-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <div class="text-2xl font-bold text-gray-900">{{ $success }}</div>
                <div class="text-[10px] text-green-600 uppercase tracking-widest font-bold">Success</div>
            </div>
        </div>
        <div class="card p-5 flex items-center gap-4 border-l-4 border-l-red-500">
            <div class="w-10 h-10 rounded-lg bg-red-50 flex items-center justify-center flex-shrink-0 text-red-500">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <div class="text-2xl font-bold text-gray-900">{{ $errors }}</div>
                <div class="text-[10px] text-red-500 uppercase tracking-widest font-bold">Errors</div>
            </div>
        </div>
        <div class="card p-5 flex items-center gap-4">
            <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0 text-blue-500">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <div class="text-2xl font-bold text-gray-900">{{ $avgDuration }}<span class="text-xs ml-1 opacity-50">ms</span></div>
                <div class="text-[10px] text-blue-500 uppercase tracking-widest font-bold">Avg Speed</div>
            </div>
        </div>
    </div>

    <div class="card overflow-hidden shadow-sm">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
            <h3 class="font-bold text-gray-900 text-sm italic">Real-time Logs</h3>
            <span class="text-[10px] text-blue-600 font-bold bg-blue-50 px-2 py-1 rounded border border-blue-100 uppercase tracking-widest">Live Feed</span>
        </div>
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-white border-b border-gray-100 text-[10px] font-black text-gray-400 uppercase tracking-widest">
                    <th class="text-left px-6 py-4">Method</th>
                    <th class="text-left px-6 py-4">Endpoint</th>
                    <th class="text-left px-6 py-4">Status</th>
                    <th class="text-left px-6 py-4">Time</th>
                    <th class="text-right px-6 py-4">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach ($logs as $log)
                    <tr class="hover:bg-blue-50/50 transition-all group overflow-hidden">
                        <td class="px-6 py-4">
                            <span class="text-[10px] font-bold font-mono px-2 py-1 rounded bg-gray-100 text-gray-500 border border-gray-200">
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
                            <a href="{{ route('admin.apilogs.show', $log->id) }}"
                                class="inline-flex items-center gap-2 bg-blue-600 text-white text-[10px] font-bold px-4 py-2 rounded-lg shadow-sm hover:bg-blue-700 transition-all uppercase tracking-widest group-hover:px-6">
                                <span>Inspect</span>
                                <svg class="w-3 h-3 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-app-layout>