<div class="space-y-4">

    {{-- FILTER BAR --}}
    <div class="flex flex-wrap items-center gap-2">
        <input
            wire:model.live.debounce.400ms="search"
            type="text"
            placeholder="Search fee ID or name…"
            class="w-full max-w-xs rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)]"
        >

        <select
            wire:model.live="statusFilter"
            class="rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)]"
        >
            <option value="">All Statuses</option>
            <option value="Active">Active</option>
            <option value="Inactive">Inactive</option>
        </select>

        {{-- <button
            type="button"
            wire:click="resetTable"
            class="rounded-xl border border-slate-300 px-4 py-2 text-sm font-bold text-slate-600 hover:bg-slate-50"
        >Reset</button> --}}
    </div>

    {{-- TABLE --}}
    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 text-left text-[11px] uppercase tracking-wide text-slate-500">
                        <th class="px-4 py-3 font-bold">Fee ID</th>
                        <th class="px-4 py-3 font-bold">Fee Name</th>
                        <th class="px-4 py-3 text-right font-bold">Cost</th>
                        <th class="px-4 py-3 font-bold">Status</th>
                        <th class="px-4 py-3 text-right font-bold">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($fees as $fee)
                        @php($isActive = strtolower(trim((string) $fee->status)) === 'active')
                        @php($word = $isActive ? 'DEACTIVATE' : 'ACTIVATE')

                        <tr wire:key="fee-row-{{ $fee->FeeID }}" class="hover:bg-slate-50/60">
                            <td class="px-4 py-3 font-mono text-xs text-slate-500">
                                {{ $fee->FeeID }}
                            </td>
                            <td class="px-4 py-3 font-semibold text-slate-700">
                                {{ $fee->FeeName }}
                            </td>
                            <td class="px-4 py-3 text-right font-bold text-slate-800">
                                ₱{{ number_format((float) $fee->Cost, 2) }}
                            </td>
                            <td class="px-4 py-3">
                                @if ($isActive)
                                    <span class="inline-flex items-center gap-1 rounded-full bg-[var(--color-primary-tint)] px-2.5 py-0.5 text-[11px] font-bold text-[var(--color-primary)]">● Active</span>
                                @else
                                    <span class="inline-flex items-center gap-1 rounded-full bg-slate-200 px-2.5 py-0.5 text-[11px] font-bold text-slate-700">● {{ $fee->status ?: 'Inactive' }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex justify-end">
                                    @if ($this->canChangeStatus)
                                        <button
                                            type="button"
                                            x-data
                                            @click="
                                                if (! window.SwalFlow) return;
                                                SwalFlow.prompt({
                                                    title: @js(($isActive ? 'Deactivate ' : 'Activate ').$fee->FeeName.'?'),
                                                    text: @js('Type '.$word.' to confirm.'),
                                                    icon: 'warning',
                                                    input: 'text',
                                                    inputLabel: @js('Type '.$word),
                                                    inputPlaceholder: @js($word),
                                                    required: true,
                                                    requiredMessage: @js('Type '.$word.' to confirm.'),
                                                    maxLength: 20,
                                                    confirmText: @js($isActive ? 'Deactivate' : 'Activate'),
                                                    cancelText: 'Cancel',
                                                    loadingTitle: 'Updating status...',
                                                    loadingText: 'Saving the new status.',
                                                }, (typed) => {
                                                    if (String(typed || '').trim().toUpperCase() !== @js($word)) {
                                                        if (window.Swal) {
                                                            Swal.fire({
                                                                icon: 'error',
                                                                title: 'Confirmation does not match',
                                                                text: @js('You must type '.$word.'. Nothing was changed.'),
                                                            });
                                                        }
                                                        return;
                                                    }
                                                    $wire.call('toggleStatus', @js($fee->FeeID), typed);
                                                });
                                            "
                                            role="switch"
                                            aria-checked="{{ $isActive ? 'true' : 'false' }}"
                                            title="{{ $isActive ? 'Click to deactivate' : 'Click to activate' }}"
                                            class="relative inline-flex h-6 w-11 items-center rounded-full transition
                                                {{ $isActive ? 'bg-[var(--color-primary)]' : 'bg-slate-300' }}"
                                        >
                                            <span class="inline-block h-4 w-4 transform rounded-full bg-white shadow transition
                                                {{ $isActive ? 'translate-x-6' : 'translate-x-1' }}"></span>
                                        </button>
                                    @else
                                        <span class="text-[11px] text-slate-500">—</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-10 text-center text-sm text-slate-500">
                                No membership fees found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($fees->hasPages())
            <div class="border-t border-slate-100 px-4 py-3">
                {{ $fees->links() }}
            </div>
        @endif
    </div>
</div>
