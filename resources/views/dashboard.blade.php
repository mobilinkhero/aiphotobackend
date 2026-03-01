<x-app-layout>
    <x-slot name="header">Overview</x-slot>
    <x-slot name="description">Monitor your app performance and statistics in real-time.</x-slot>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8 mt-2">
        <!-- Stat Card 1 -->
        <div class="card p-6 flex flex-col justify-between">
            <div>
                <div class="text-sm font-medium text-gray-500 mb-1">Total Enhancements</div>
                <div class="text-3xl font-bold text-gray-900 tracking-tight">42,891</div>
            </div>
            <div class="mt-4 flex items-center justify-between border-t border-gray-100 pt-4">
                <span class="text-xs text-gray-400">Since launch</span>
                <span
                    class="text-xs font-semibold text-green-600 flex items-center gap-1 bg-green-50 px-2 py-0.5 rounded">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                            d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                    12.5%
                </span>
            </div>
        </div>

        <!-- Stat Card 2 -->
        <div class="card p-6 flex flex-col justify-between">
            <div>
                <div class="text-sm font-medium text-gray-500 mb-1">Active Installs</div>
                <div class="text-3xl font-bold text-gray-900 tracking-tight">1,402</div>
            </div>
            <div class="mt-4 flex items-center justify-between border-t border-gray-100 pt-4">
                <span class="text-xs text-gray-400">This week</span>
                <span
                    class="text-xs font-semibold text-green-600 flex items-center gap-1 bg-green-50 px-2 py-0.5 rounded">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                            d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                    4.2%
                </span>
            </div>
        </div>

        <!-- Stat Card 3 -->
        <div class="card p-6 flex flex-col justify-between">
            <div>
                <div class="text-sm font-medium text-gray-500 mb-1">API Error Rate</div>
                <div class="text-3xl font-bold text-gray-900 tracking-tight">0.04%</div>
            </div>
            <div class="mt-4 flex items-center justify-between border-t border-gray-100 pt-4">
                <span class="text-xs text-gray-400">Last 24 hours</span>
                <span
                    class="text-xs font-semibold text-gray-600 flex items-center gap-1 bg-gray-100 px-2 py-0.5 rounded">
                    <svg class="w-3 h-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Healthy
                </span>
            </div>
        </div>
    </div>

    <!-- Charts / Lists placeholders -->
    <div class="card h-80 flex flex-col items-center justify-center p-6 text-center">
        <div class="w-12 h-12 rounded-full bg-gray-50 flex items-center justify-center mb-3">
            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
            </svg>
        </div>
        <div class="text-base font-medium text-gray-900 mb-1">Usage Graph Unavailable</div>
        <div class="text-sm text-gray-500 max-w-sm">Connect your database usage logs completely to view generation
            timelines here.</div>
        <button
            class="mt-4 px-4 py-2 bg-white border border-gray-200 text-gray-700 text-sm font-semibold rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900 transition flex gap-2">
            Configure integration
        </button>
    </div>
</x-app-layout>