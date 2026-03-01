<x-app-layout>
    <x-slot name="header">App Feature Toggles</x-slot>
    <x-slot name="description">Instantly hide, show, or pay-wall mobile app features without requiring an app store
        update.</x-slot>

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

    <form method="POST" action="{{ route('admin.settings.features.update') }}">
        @csrf
        <input type="hidden" name="setting_group" value="features">

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            @foreach ($features as $id => $data)
                <div
                    class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm hover:border-blue-300 transition-colors flex flex-col">
                    <div class="px-5 py-4 border-b border-gray-100 bg-gray-50 flex items-center gap-3">
                        <div
                            class="w-10 h-10 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center shrink-0">
                            {!! $data['icon'] !!}
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900">{{ $data['title'] }}</h3>
                            <p class="text-[11px] text-gray-400 font-mono mt-0.5">{{ $id }}</p>
                        </div>
                    </div>
                    <div class="p-5 flex flex-col gap-5 flex-1 justify-center">
                        <!-- Enabled Toggle -->
                        <div class="flex items-center justify-between">
                            <div>
                                <label for="{{ $id }}_enabled"
                                    class="text-sm font-semibold text-gray-700 cursor-pointer block">
                                    Show Feature in App
                                </label>
                                <p class="text-xs text-gray-400 mt-1">If disabled, completely hidden</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" id="{{ $id }}_enabled" name="feature_{{ $id }}_enabled" value="1"
                                    class="sr-only peer" {{ ($settings["feature_{$id}_enabled"] ?? '0') == '1' ? 'checked' : '' }}>
                                <div
                                    class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600">
                                </div>
                            </label>
                        </div>

                        <!-- Premium Toggle -->
                        <div class="flex items-center justify-between pt-5 border-t border-gray-100">
                            <div>
                                <label for="{{ $id }}_premium"
                                    class="text-sm font-semibold text-purple-700 flex items-center gap-1.5 cursor-pointer">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                        </path>
                                    </svg>
                                    PRO Feature
                                </label>
                                <p class="text-[11px] text-gray-400 mt-1">Require PRO subscription</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" id="{{ $id }}_premium" name="feature_{{ $id }}_premium" value="1"
                                    class="sr-only peer" {{ ($settings["feature_{$id}_premium"] ?? '0') == '1' ? 'checked' : '' }}>
                                <div
                                    class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-purple-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-600">
                                </div>
                            </label>
                        </div>

                        <!-- Coins Value -->
                        <div class="flex items-center justify-between pt-5 border-t border-gray-100">
                            <div>
                                <label for="{{ $id }}_coins"
                                    class="text-sm font-semibold text-gray-700 flex items-center gap-1.5 cursor-pointer">
                                    <svg class="w-4 h-4 text-orange-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 2a8 8 0 100 16 8 8 0 000-16zM8 7a1 1 0 112 0v1h1a1 1 0 010 2H9v1a1 1 0 11-2 0v-1H6a1 1 0 110-2h1V7z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    Coin Cost
                                </label>
                                <p class="text-[11px] text-gray-400 mt-1">When not PRO</p>
                            </div>
                            <div>
                                <input type="number" id="{{ $id }}_coins" name="feature_{{ $id }}_coins"
                                    value="{{ $settings["feature_{$id}_coins"] ?? '1' }}" min="0" step="1"
                                    class="w-16 h-8 text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 font-mono text-center pb-1">
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="flex justify-end mb-16">
            <button type="submit"
                class="bg-gray-900 text-white px-6 py-2.5 rounded-md font-semibold text-sm hover:bg-gray-800 transition flex items-center gap-2 transform active:scale-95 shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900 border border-transparent">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4">
                    </path>
                </svg>
                Save Feature Config
            </button>
        </div>
    </form>
</x-app-layout>