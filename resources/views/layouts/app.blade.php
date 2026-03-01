<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Truni AI - Admin Control Panel</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f9fafb;
            /* gray-50 */
            color: #111827;
            /* gray-900 */
        }

        .sidebar-item {
            color: #4b5563;
            /* gray-600 */
            transition: all 0.15s ease-in-out;
            border-radius: 6px;
        }

        .sidebar-item:hover {
            background-color: #f3f4f6;
            /* gray-100 */
            color: #111827;
            /* gray-900 */
        }

        .sidebar-item.active {
            background-color: #f3f4f6;
            /* gray-100 */
            color: #111827;
            /* gray-900 */
            font-weight: 500;
        }

        .card {
            background-color: #ffffff;
            border: 1px solid #e5e7eb;
            /* gray-200 */
            border-radius: 8px;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        }
    </style>
</head>

<body class="antialiased flex h-screen overflow-hidden selection:bg-blue-100 selection:text-blue-900">

    <!-- Sidebar -->
    <aside class="w-64 border-r border-gray-200 bg-white flex flex-col justify-between hidden md:flex z-20">
        <div class="px-4 py-6">
            <div class="flex items-center gap-2 mb-8 px-2">
                <div class="w-8 h-8 rounded bg-gray-900 flex items-center justify-center font-bold text-white text-sm">
                    T
                </div>
                <span class="text-lg font-semibold tracking-tight text-gray-900">Truni AI</span>
            </div>

            <nav class="space-y-1">
                <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2 px-2">Menu</div>

                <a href="{{ route('dashboard') }}"
                    class="flex items-center gap-3 px-3 py-2 text-sm sidebar-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <svg class="w-4 h-4 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                        </path>
                    </svg>
                    Overview
                </a>

                <a href="{{ route('admin.enhancements') }}"
                    class="flex items-center gap-3 px-3 py-2 text-sm sidebar-item {{ request()->routeIs('admin.enhancements') ? 'active' : '' }}">
                    <svg class="w-4 h-4 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                        </path>
                    </svg>
                    Enhancements
                </a>

                <a href="{{ route('admin.apilogs') }}"
                    class="flex items-center gap-3 px-3 py-2 text-sm sidebar-item {{ request()->routeIs('admin.apilogs') ? 'active' : '' }}">
                    <svg class="w-4 h-4 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4">
                        </path>
                    </svg>
                    API Logs
                </a>

                <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2 mt-6 px-2">Settings</div>
                <!-- System Config -->
                <a href="{{ route('admin.settings.system') }}"
                    class="flex items-center gap-3 px-3 py-2 text-sm sidebar-item {{ request()->routeIs('admin.settings.system') ? 'active' : '' }}">
                    <svg class="w-4 h-4 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                        </path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    System Config
                </a>
                <!-- AdMob Config -->
                <a href="{{ route('admin.settings.admob') }}"
                    class="flex items-center gap-3 px-3 py-2 text-sm sidebar-item {{ request()->routeIs('admin.settings.admob') ? 'active' : '' }}">
                    <svg class="w-4 h-4 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z">
                        </path>
                    </svg>
                    AdMob Config
                </a>
                <!-- AI Config -->
                <a href="{{ route('admin.settings.ai') }}"
                    class="flex items-center gap-3 px-3 py-2 text-sm sidebar-item {{ request()->routeIs('admin.settings.ai') ? 'active' : '' }}">
                    <svg class="w-4 h-4 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z">
                        </path>
                    </svg>
                    AI Processing
                </a>
                <!-- AI Test -->
                <a href="{{ route('admin.ai.test') }}"
                    class="flex items-center gap-3 px-3 py-2 text-sm sidebar-item {{ request()->routeIs('admin.ai.test') ? 'active' : '' }}">
                    <svg class="w-4 h-4 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                        </path>
                    </svg>
                    AI Test Lab
                </a>
            </nav>
        </div>

        <div class="px-4 py-4 border-t border-gray-100">
            <!-- User Profile / Logout -->
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div
                        class="w-8 h-8 rounded-full bg-gray-100 border border-gray-200 flex items-center justify-center text-sm font-semibold text-gray-700">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                    <div class="text-sm">
                        <div class="text-gray-900 font-medium truncate w-24">
                            {{ Auth::user()->name }}
                        </div>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="text-gray-400 hover:text-gray-900 p-1.5 rounded-md hover:bg-gray-100 transition-colors"
                        title="Logout">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                            </path>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 flex flex-col overflow-hidden bg-[#f9fafb]">
        <!-- Header -->
        @isset($header)
            <header
                class="bg-white border-b border-gray-200 px-8 py-5 sticky top-0 z-10 w-full flex justify-between items-center">
                <div>
                    <h1 class="text-xl font-semibold text-gray-900">{{ $header }}</h1>
                    @isset($description)
                        <p class="text-sm text-gray-500 mt-1 max-w-2xl">{{ $description }}</p>
                    @endisset
                </div>
            </header>
        @endisset

        <!-- Content scroll area -->
        <div class="flex-1 overflow-y-auto p-8 relative">
            <div class="max-w-[1200px] mx-auto relative z-10">
                {{ $slot }}
            </div>
        </div>
    </main>
</body>

</html>