<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="p-2 bg-indigo-600 rounded-lg shadow-lg shadow-indigo-200">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
            </div>
            <span>AI Magic Lab</span>
        </div>
    </x-slot>
    <x-slot name="description">Test your premium AI enhancement tools in real-time. Secure, fast, and
        high-fidelity.</x-slot>

    <div class="space-y-8 mt-4 pb-12">
        {{-- Status / Alerts --}}
        @if(empty($apiKey))
            <div class="bg-amber-50 border border-amber-100 p-4 rounded-xl flex items-start gap-4 shadow-sm animate-pulse">
                <div class="p-2 bg-amber-100 rounded-lg text-amber-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
                    </svg>
                </div>
                <div>
                    <h4 class="text-sm font-bold text-amber-900 leading-tight">AI Provider Needs Configuration</h4>
                    <p class="text-xs text-amber-700 mt-0.5">Please add your Replicate API key in settings to unlock these
                        features.</p>
                    <a href="{{ route('admin.settings.ai') }}"
                        class="inline-flex items-center text-xs font-bold text-amber-600 hover:text-amber-700 mt-2 uppercase tracking-wider">Configure
                        Now →</a>
                </div>
            </div>
        @endif

        @if(isset($error))
            <div class="bg-rose-50 border border-rose-100 p-4 rounded-xl flex items-start gap-4 shadow-sm">
                <div class="p-2 bg-rose-100 rounded-lg text-rose-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </div>
                <div>
                    <h4 class="text-sm font-bold text-rose-900 leading-tight">AI Error Encountered</h4>
                    <p class="text-xs text-rose-700 mt-0.5">{{ $error }}</p>
                </div>
            </div>
        @endif

        {{-- Main Interface --}}
        <div class="grid grid-cols-1 xl:grid-cols-12 gap-8 items-start">

            {{-- Control Panel --}}
            <div class="xl:col-span-5 space-y-6">
                <div class="bg-white border border-gray-100 rounded-2xl shadow-sm overflow-hidden">
                    <div class="p-6">
                        <form method="POST" action="{{ route('admin.ai.test.run') }}" enctype="multipart/form-data"
                            id="testForm" class="space-y-8">
                            @csrf

                            {{-- Step 1: Upload --}}
                            <div class="space-y-3">
                                <div class="flex items-center justify-between">
                                    <label class="text-xs font-bold uppercase tracking-widest text-indigo-600">01.
                                        Upload Source</label>
                                    <span class="text-[10px] text-gray-400 font-mono">MAX 10MB</span>
                                </div>
                                <div id="dropzone" class="relative group">
                                    <div
                                        class="border-2 border-dashed border-gray-100 hover:border-indigo-200 rounded-2xl h-56 flex flex-col items-center justify-center cursor-pointer transition-all bg-gray-50/50 relative overflow-hidden">
                                        <img id="previewImg" src=""
                                            class="hidden absolute inset-0 w-full h-full object-cover rounded-2xl z-10">

                                        <div id="dropPlaceholder"
                                            class="flex flex-col items-center gap-3 text-center px-6">
                                            <div
                                                class="p-4 bg-white rounded-2xl shadow-sm group-hover:scale-110 transition-transform duration-300">
                                                <svg class="w-8 h-8 text-indigo-500" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="1.5"
                                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                                    </path>
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="text-sm font-bold text-gray-900 tracking-tight">Drop your
                                                    photo here</p>
                                                <p class="text-xs text-gray-500 mt-1">or click to browse local files</p>
                                            </div>
                                        </div>

                                        <input type="file" name="image" id="imageInput" accept="image/*"
                                            class="absolute inset-0 opacity-0 cursor-pointer z-20">
                                    </div>
                                </div>
                            </div>

                            {{-- Step 2: Select Engine --}}
                            <div class="space-y-4">
                                <label for="toolSelect"
                                    class="text-xs font-bold uppercase tracking-widest text-indigo-600">02. Choose AI
                                    Engine</label>
                                <div class="relative">
                                    <select name="tool" id="toolSelect"
                                        class="w-full pl-4 pr-10 py-4 bg-gray-50 border-2 border-gray-100 rounded-2xl text-sm font-bold text-gray-900 focus:border-indigo-600 focus:ring-4 focus:ring-indigo-600/5 transition-all appearance-none cursor-pointer">
                                        @foreach($tools as $tool)
                                            <option value="{{ $tool }}" {{ $loop->first ? 'selected' : '' }}>
                                                {{ strtoupper($tool) }} Enhancement
                                            </option>
                                        @endforeach
                                    </select>
                                    <div
                                        class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-gray-400">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            {{-- Actions --}}
                            <div class="pt-4">
                                <button type="submit" id="submitBtn"
                                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white p-4 rounded-xl font-bold text-sm shadow-lg shadow-indigo-100 transition-all flex items-center justify-center gap-3 transform active:scale-[0.98]">
                                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 10V3L4 14h7v7l9-11h-7z" />
                                    </svg>
                                    <span id="btnText">RUN {{ strtoupper($tools[0] ?? 'AI') }} ENHANCEMENT</span>
                                </button>
                                <p
                                    class="text-[10px] text-gray-400 text-center mt-4 uppercase font-bold tracking-widest">
                                    Processing takes approx. 10s - 30s</p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Viewer Panel --}}
            <div class="xl:col-span-7 h-full">
                <div
                    class="bg-gray-900 rounded-[2rem] p-4 shadow-2xl relative min-h-[500px] flex flex-col group overflow-hidden border border-gray-800">
                    {{-- UI Decorations --}}
                    <div class="absolute top-6 left-6 flex items-center gap-2 z-10">
                        <div class="w-2.5 h-2.5 rounded-full bg-rose-500 shadow-sm"></div>
                        <div class="w-2.5 h-2.5 rounded-full bg-amber-500 shadow-sm"></div>
                        <div class="w-2.5 h-2.5 rounded-full bg-emerald-500 shadow-sm"></div>
                        <span class="text-[10px] font-mono text-gray-500 ml-2 uppercase tracking-widest">AI Result
                            Viewer v1.0</span>
                    </div>

                    <div class="flex-1 flex items-center justify-center p-8">
                        @if(isset($resultUrl))
                            <div class="w-full space-y-6 animate-in fade-in zoom-in duration-500">
                                <div class="relative group/img overflow-hidden rounded-3xl shadow-2xl ring-1 ring-white/10">
                                    <img src="{{ $resultUrl }}" alt="AI Result"
                                        class="w-full max-h-[60vh] object-contain bg-black/40 blur-0 active:scale-110 transition-all cursor-zoom-in">
                                    <div
                                        class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover/img:opacity-100 transition-opacity flex items-end p-8">
                                        <p class="text-white text-xs font-medium tracking-wide">High fidelity AI
                                            reconstruction complete.</p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <a href="{{ $resultUrl }}" download
                                        class="bg-white/10 hover:bg-white/20 text-white text-center py-3.5 rounded-2xl font-bold text-sm backdrop-blur-md transition-all border border-white/10 flex items-center justify-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                        </svg>
                                        Save Image
                                    </a>
                                    <a href="{{ $resultUrl }}" target="_blank"
                                        class="bg-indigo-600 hover:bg-indigo-500 text-white text-center py-3.5 rounded-2xl font-bold text-sm shadow-lg shadow-indigo-600/20 transition-all flex items-center justify-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                            </path>
                                        </svg>
                                        Full Canvas
                                    </a>
                                </div>
                            </div>
                        @else
                            <div class="flex flex-col items-center text-center max-w-xs space-y-6">
                                <div
                                    class="w-24 h-24 bg-gray-800/50 rounded-[2rem] flex items-center justify-center text-gray-700 animate-pulse ring-1 ring-white/5">
                                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.035-.259a3.375 3.375 0 002.456-2.455L18 2.25l.259 1.035a3.375 3.375 0 002.455 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.455z">
                                        </path>
                                    </svg>
                                </div>
                                <div class="space-y-2">
                                    <h5 class="text-sm font-bold text-gray-400 uppercase tracking-widest">Ready for Magic
                                    </h5>
                                    <p class="text-xs text-gray-600 font-medium px-4 leading-relaxed">Your enhanced result
                                        will appear in high definition here. Load a photo to begin.</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script>
        // Update button text on tool selection
        const toolSelect = document.getElementById('toolSelect');
        const btnText = document.getElementById('btnText');

        if (toolSelect && btnText) {
            toolSelect.addEventListener('change', function() {
                btnText.innerText = `RUN ${this.value.toUpperCase()} ENHANCEMENT`;
            });
        }

        // Image preview on select
        document.getElementById('imageInput').addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = function (e) {
                const img = document.getElementById('previewImg');
                img.src = e.target.result;
                img.classList.remove('hidden');
                const placeholder = document.getElementById('dropPlaceholder');
                if (placeholder) placeholder.classList.add('hidden');
            };
            reader.readAsDataURL(file);
        });

        // Show loading state on submit
        document.getElementById('testForm').addEventListener('submit', function () {
            const btn = document.getElementById('submitBtn');
            btn.innerHTML = `
                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                AI Processing...
            `;
            btn.classList.add('opacity-80', 'cursor-wait');
            btn.disabled = true;
        });
    </script>
</x-app-layout>