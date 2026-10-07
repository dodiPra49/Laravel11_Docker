<!-- Small App Version & Latest Commit Card (Top-Right Corner) -->
<div id="app-version-card" class="fixed top-3 right-3 sm:top-5 sm:right-6 z-50 transition-all duration-300 max-w-[calc(100vw-1.5rem)] sm:max-w-xs">
    <div class="glass-card bg-slate-900/85 backdrop-blur-xl border border-slate-700/60 shadow-2xl rounded-2xl p-3 sm:p-3.5 text-xs text-slate-200 transition-all duration-200 hover:border-indigo-500/50">
        <!-- Card Header: Version & Toggle Button -->
        <div class="flex items-center justify-between gap-3">
            <div class="flex items-center gap-2">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                </span>
                <span class="font-bold tracking-tight text-white flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                    </svg>
                    <span>{{ $appInfo['version'] ?? 'v1.0.0' }}</span>
                </span>
                <span class="px-1.5 py-0.5 text-[10px] font-semibold bg-indigo-500/20 text-indigo-300 rounded border border-indigo-500/30">
                    {{ $appInfo['branch'] ?? 'main' }}
                </span>
            </div>

            <!-- Minimize / Expand Toggle Button -->
            <button type="button" 
                    id="version-card-toggle" 
                    class="text-slate-400 hover:text-white p-1 rounded-lg hover:bg-slate-800/80 transition-colors focus:outline-none"
                    title="Sembunyikan / Tampilkan detail"
                    aria-label="Toggle details">
                <svg id="toggle-chevron" class="w-3.5 h-3.5 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>
        </div>

        <!-- Collapsible Content: Last Commit & Framework Info -->
        <div id="version-card-details" class="mt-2.5 pt-2.5 border-t border-slate-700/60 space-y-2">
            <div>
                <div class="flex items-center justify-between text-[11px] text-slate-400 mb-0.5">
                    <span class="flex items-center gap-1">
                        <!-- Git Commit Icon -->
                        <svg class="w-3.5 h-3.5 text-sky-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                        <span class="font-medium text-slate-300">Commit Terakhir:</span>
                    </span>
                    <span class="font-mono text-[10px] text-indigo-300 bg-indigo-950/60 px-1.5 py-0.5 rounded border border-indigo-800/40" title="Commit Hash">
                        #{{ $appInfo['commit_hash'] ?? 'latest' }}
                    </span>
                </div>
                
                <!-- Commit Message -->
                <p class="text-[11px] text-slate-200 font-medium leading-snug line-clamp-2 bg-slate-950/40 p-2 rounded-lg border border-slate-800" title="{{ $appInfo['commit_message'] ?? '' }}">
                    "{{ $appInfo['commit_message'] ?? 'Initial commit' }}"
                </p>
            </div>

            <!-- Footer Meta: Date & Laravel Version -->
            <div class="flex items-center justify-between text-[10px] text-slate-400 pt-0.5">
                @if (!empty($appInfo['commit_date']))
                    <span class="flex items-center gap-1 text-slate-400">
                        <svg class="w-3 h-3 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        {{ $appInfo['commit_date'] }}
                    </span>
                @else
                    <span></span>
                @endif
                <span class="text-slate-500">
                    Laravel {{ $appInfo['laravel_version'] ?? app()->version() }}
                </span>
            </div>
        </div>
    </div>
</div>

<script>
    (function() {
        const toggleBtn = document.getElementById('version-card-toggle');
        const details = document.getElementById('version-card-details');
        const chevron = document.getElementById('toggle-chevron');
        
        if (!toggleBtn || !details || !chevron) return;

        // Restore collapsed state from localStorage if user preferred it collapsed
        const isCollapsed = localStorage.getItem('app_version_card_collapsed') === 'true';
        if (isCollapsed) {
            details.classList.add('hidden');
            chevron.style.transform = 'rotate(-90deg)';
        }

        toggleBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            const currentlyHidden = details.classList.contains('hidden');
            if (currentlyHidden) {
                details.classList.remove('hidden');
                chevron.style.transform = 'rotate(0deg)';
                localStorage.setItem('app_version_card_collapsed', 'false');
            } else {
                details.classList.add('hidden');
                chevron.style.transform = 'rotate(-90deg)';
                localStorage.setItem('app_version_card_collapsed', 'true');
            }
        });
    })();
</script>
