<div class="w-full bg-slate-50 min-h-[500px] p-6 rounded-2xl border border-slate-200/80 shadow-sm">
    {{-- Top Action Bar --}}
    <div class="flex items-center justify-between mb-6 pb-4 border-b border-slate-200">
        <div>
            <h2 class="text-lg font-bold text-slate-800">Genealogy Tree</h2>
            <p class="text-xs text-slate-500">Visualizing direct referrals and downlines</p>
        </div>

        @if(isset($tree['id']) && $tree['id'] !== auth()->id())
            <button
                wire:click="focusUser({{ auth()->id() }})"
                class="px-3 py-1.5 text-xs font-semibold text-blue-600 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors border border-blue-200"
            >
                ↺ Reset to My Node
            </button>
        @endif
    </div>

    {{-- Tree Viewport --}}
    <div class="w-full overflow-x-auto py-6">
        @if(!empty($tree))
            <div class="inline-flex min-w-full justify-center">
                {{-- Corrected path to match genealogy partials directory --}}
                @include('livewire.member-pages.genealogy.partials.tree-node', [
                    'node' => $tree,
                    'isRoot' => true
                ])
            </div>
        @else
            <div class="flex flex-col items-center justify-center py-16 text-slate-400 space-y-2">
                <svg class="w-12 h-12 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                <p class="text-sm font-medium">No downline record found for this account.</p>
            </div>
        @endif
    </div>
</div>
