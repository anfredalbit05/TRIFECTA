<div class="reports-container w-full px-4 sm:px-6 lg:px-8 py-6">

    <!-- HEADER -->
    <div class="members-header flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-2">
        <h2 class="report-title text-2xl font-bold text-slate-800">
            Ledgers
        </h2>
    </div>

    <p class="report-description text-sm text-slate-500 mb-6">
        View and monitor system financial ledger entries.
    </p>

    <!-- SEARCH INPUT & REFRESH BUTTON CONTAINER -->
    <div class="flex items-center justify-between gap-4 mb-6">
        <!-- SEARCH INPUT -->
        <div class="relative max-w-sm w-full">
            <span class="absolute inset-y-0 left-3 flex items-center text-slate-400 pointer-events-none">
                🔍
            </span>
            <input type="text" wire:model.live="search" placeholder="Search ledgers..."
                class="w-full pl-10 pr-4 py-2 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0C2C6B] bg-white shadow-sm">
        </div>

        <!-- REFRESH BUTTON -->
        <button wire:click="resetTable" wire:loading.attr="disabled" title="Refresh Table"
            class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 text-sm font-semibold rounded-xl shadow-sm transition cursor-pointer">
            <span wire:loading.remove wire:target="resetTable">🔄</span>
            <span wire:loading wire:target="resetTable" class="animate-spin">⏳</span>
            <span class="hidden sm:inline">Refresh</span>
        </button>
    </div>

    <!-- LEDGERS TABLE -->
    <div class="overflow-x-auto bg-white rounded-xl border border-slate-200 shadow-sm">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-slate-200 bg-[#0C2C6B] text-xs font-semibold uppercase tracking-wider text-white">
                    <th class="py-3 px-4">#</th>
                    <th class="py-3 px-4">Source</th>
                    <th class="py-3 px-4">Activation ID</th>
                    <th class="py-3 px-4">Ref ID</th>
                    <th class="py-3 px-4">Amount</th>
                    <th class="py-3 px-4">Status</th>
                    <th class="py-3 px-4">Date</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 text-sm text-slate-700">
                @forelse($ledgers as $index => $ledger)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="py-4 px-4 font-medium text-slate-500">
                            {{ $ledgers->firstItem() + $index }}
                        </td>

                        <td class="py-4 px-4 font-semibold text-slate-900">
                            <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-[#0C2C6B]/10 text-[#0C2C6B] border border-[#0C2C6B]/30">
                                {{ $ledger->Source }}
                            </span>
                        </td>

                        <td class="py-4 px-4 text-slate-600">
                            {{ $ledger->ActivationID ?? 'N/A' }}
                        </td>

                        <td class="py-4 px-4 text-slate-600">
                            {{ $ledger->RefID ?? 'N/A' }}
                        </td>

                        <td class="py-4 px-4 font-semibold {{ $ledger->amount < 0 ? 'text-[#D79B10]' : 'text-emerald-600' }}">
                            {{ number_format($ledger->amount, 2) }}
                        </td>

                        <td class="py-4 px-4">
                            <span class="px-2.5 py-1 text-xs font-semibold rounded-full
                                {{ $ledger->status === 'Completed' ? 'bg-[#0C2C6B]/10 text-[#0C2C6B] border border-[#0C2C6B]/30' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                                {{ $ledger->status }}
                            </span>
                        </td>

                        <td class="py-4 px-4 text-slate-500 text-xs">
                            {{ $ledger->created_at?->format('M d, Y h:i A') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-8 text-slate-500">
                            No ledger entries available.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- PAGINATION -->
        @if ($ledgers->hasPages())
            <div class="px-4 py-4 border-t border-slate-200 bg-slate-50 flex justify-center items-center space-x-4">
                {{-- Previous Page Link --}}
                @if ($ledgers->onFirstPage())
                    <span class="px-4 py-2 bg-gray-200 text-gray-400 rounded cursor-not-allowed text-sm">
                        Previous
                    </span>
                @else
                    <button wire:click="previousPage" wire:loading.attr="disabled"
                        class="px-4 py-2 text-white rounded transition cursor-pointer text-sm"
                        style="background-color: #D79B10;">
                        Previous
                    </button>
                @endif

                <span class="text-sm text-gray-600">
                    Page {{ $ledgers->currentPage() }} of {{ $ledgers->lastPage() }}
                </span>

                {{-- Next Page Link --}}
                @if ($ledgers->hasMorePages())
                    <button wire:click="nextPage" wire:loading.attr="disabled"
                        class="px-4 py-2 text-white rounded transition cursor-pointer text-sm"
                        style="background-color: #0C2C6B;">
                        Next
                    </button>
                @else
                    <span class="px-4 py-2 bg-gray-200 text-gray-400 rounded cursor-not-allowed text-sm">
                        Next
                    </span>
                @endif
            </div>
        @endif
    </div>

</div>
