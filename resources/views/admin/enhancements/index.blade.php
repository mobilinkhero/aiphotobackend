<x-app-layout>
    <x-slot name="header">AI Enhancements</x-slot>
    <x-slot name="description">A log of all AI photo processing jobs run through the app.</x-slot>

    <!-- Stats Row -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8 mt-2">
        <div class="card p-5 flex items-center gap-4">
            <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                    </path>
                </svg>
            </div>
            <div>
                <div class="text-2xl font-bold text-gray-900">{{ $total }}</div>
                <div class="text-sm text-gray-500">Total Jobs</div>
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
                <div class="text-2xl font-bold text-gray-900">{{ $successful }}</div>
                <div class="text-sm text-gray-500">Successful</div>
            </div>
        </div>
        <div class="card p-5 flex items-center gap-4">
            <div class="w-10 h-10 rounded-lg bg-red-50 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div>
                <div class="text-2xl font-bold text-gray-900">{{ $failed }}</div>
                <div class="text-sm text-gray-500">Failed</div>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="card overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-900 text-sm">Recent Enhancement Jobs</h3>
        </div>
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-100">
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">#</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Tool
                    </th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Device
                    </th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Status
                    </th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Time
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach ($enhancements as $job)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-3 text-gray-400 font-mono text-xs">{{ $job['id'] }}</td>
                        <td class="px-6 py-3 font-medium text-gray-900">{{ $job['tool'] }}</td>
                        <td class="px-6 py-3 text-gray-600">{{ $job['device'] }}</td>
                        <td class="px-6 py-3">
                            @if($job['status'] === 'success')
                                <span
                                    class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-semibold bg-green-50 text-green-700">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Success
                                </span>
                            @elseif($job['status'] === 'failed')
                                <span
                                    class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-semibold bg-red-50 text-red-700">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Failed
                                </span>
                            @else
                                <span
                                    class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-semibold bg-yellow-50 text-yellow-700">
                                    <span class="w-1.5 h-1.5 rounded-full bg-yellow-400"></span> Pending
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-3 text-gray-400 text-xs">{{ $job['created_at']->diffForHumans() }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-app-layout>