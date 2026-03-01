<x-app-layout>
    <x-slot name="header">{{ $title }}</x-slot>
    <x-slot name="description">{{ $description }}</x-slot>

    @if (session('success'))
        <div
            class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-6 flex items-center gap-3 shadow-sm mt-2">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span class="font-medium text-sm">{{ session('success') }}</span>
        </div>
    @endif

    <form method="POST" action="{{ route($updateRoute) }}">
        @csrf

        <!-- Pass the group dynamically so the controller knows which booleans to reset -->
        @if ($settings->isNotEmpty())
            <input type="hidden" name="setting_group" value="{{ $defaultSettings[$settings->first()->key]['group'] }}">
        @endif

        <div class="card mb-8">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50/50 flex justify-between items-center">
                <div>
                    <h3 class="font-semibold text-gray-900 tracking-tight text-base">{{ $title }}</h3>
                    <p class="text-sm text-gray-500 mt-1">Make changes below and click save to deploy immediately.</p>
                </div>
            </div>

            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-8">
                @foreach ($settings as $setting)
                    @php
                        $meta = $defaultSettings[$setting->key] ?? ['type' => 'text', 'desc' => $setting->key];
                    @endphp

                    <div class="flex flex-col justify-center gap-1.5 focus-within:text-blue-600 transition-colors h-full">
                        @if($meta['type'] == 'boolean')
                            <label
                                class="flex items-center justify-between cursor-pointer p-4 border border-gray-200 rounded-md bg-white hover:border-blue-400 transition-colors h-full shadow-sm group">
                                <div class="pr-6">
                                    <div class="text-sm font-semibold text-gray-900">{{ $meta['desc'] }}</div>
                                    <div
                                        class="text-[11px] text-gray-400 font-mono mt-1 group-hover:text-gray-500 transition-colors">
                                        {{ $setting->key }}</div>
                                </div>
                                <div class="relative shrink-0">
                                    <input type="checkbox" name="{{ $setting->key }}" value="1" class="sr-only peer" {{ $setting->value == '1' ? 'checked' : '' }}>
                                    <div
                                        class="w-11 h-6 bg-gray-200 rounded-full peer peer-focus:ring-2 peer-focus:ring-blue-500 peer-focus:ring-offset-2 peer-checked:bg-blue-600 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border after:border-gray-300 after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-full peer-checked:after:border-white">
                                    </div>
                                </div>
                            </label>
                        @elseif($meta['type'] == 'password')
                            <label
                                class="text-xs font-medium uppercase tracking-wider text-gray-500">{{ $meta['desc'] }}</label>
                            <div class="relative group">
                                <input type="password" name="{{ $setting->key }}" value="{{ $setting->value }}"
                                    class="w-full bg-white border border-gray-300 rounded-md pl-10 pr-4 py-2 text-sm text-gray-900 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition-all placeholder:text-gray-400 shadow-sm font-mono"
                                    placeholder="sk_test_...">
                                <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 transform -translate-y-1/2 group-focus-within:text-blue-600 transition-colors"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                                    </path>
                                </svg>
                            </div>
                            <div class="text-[11px] text-gray-400 font-mono pl-1 mt-0.5">{{ $setting->key }}</div>
                        @else
                            <label
                                class="text-xs font-medium uppercase tracking-wider text-gray-500">{{ $meta['desc'] }}</label>
                            <div class="relative group">
                                <input type="text" name="{{ $setting->key }}" value="{{ $setting->value }}"
                                    class="w-full bg-white border border-gray-300 rounded-md pl-10 pr-4 py-2 text-sm text-gray-900 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition-all placeholder:text-gray-400 shadow-sm"
                                    placeholder="e.g. 1.0.0">
                                <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 transform -translate-y-1/2 group-focus-within:text-blue-600 transition-colors"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37...">
                                    </path>
                                </svg>
                            </div>
                            <div class="text-[11px] text-gray-400 font-mono pl-1 mt-0.5">{{ $setting->key }}</div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        <div class="flex justify-end mb-16">
            <button type="submit"
                class="bg-gray-900 text-white px-6 py-2.5 rounded-md font-semibold text-sm hover:bg-gray-800 transition flex items-center gap-2 transform active:scale-95 shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900 border border-transparent">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4">
                    </path>
                </svg>
                Save Configuration
            </button>
        </div>
    </form>
</x-app-layout>