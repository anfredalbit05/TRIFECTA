
<div class="branches-container w-full px-4 sm:px-6 lg:px-8 py-6">

    {{-- Header --}}
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Branches</h2>
            <p class="mt-1 text-sm text-slate-500">Branch management and directory.</p>
        </div>

        @if ($this->canCreate)
            <button type="button" wire:click="openCreateModal"
                class="rounded-xl bg-[#0C2C6B] px-4 py-2.5 text-sm font-semibold text-white shadow transition hover:bg-[#09204f]">
                + Add Branch
            </button>
        @endif
    </div>

    {{-- Flash message --}}
    @if (session()->has('message'))
        <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-700">
            {{ session('message') }}
        </div>
    @endif

{{-- SEARCH, DATE RANGE & REFRESH --}}
<div class="flex flex-wrap items-center justify-between gap-4 mb-6">

    {{-- SEARCH INPUT --}}
    <div class="relative max-w-sm w-full sm:w-auto flex-1">

        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400 pointer-events-none">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="m21 21-4.35-4.35m2.35-5.65a8 8 0 1 1-16 0 8 8 0 0 1 16 0Z"
                />
            </svg>
        </span>

        <input
            type="text"
            wire:model.live.debounce.300ms="search"
            placeholder="Search branches..."
            class="w-full pl-10 pr-4 py-2.5 text-sm border border-slate-200 rounded-xl
                focus:outline-none focus:ring-2 focus:ring-[#0C2C6B]
                focus:border-[#0C2C6B] bg-white shadow-sm transition"
        >
    </div>

    {{-- FILTERS AND REFRESH GROUP --}}
    <div class="flex flex-wrap items-center gap-3 w-full sm:w-auto justify-end">

        {{-- DATE RANGE FILTER --}}
        <div wire:ignore class="relative w-full sm:w-72">
            <span class="absolute inset-y-0 left-3 flex items-center text-slate-400 pointer-events-none z-10">
                📅
            </span>

            <input
                type="text"
                id="branchDateRange"
                readonly
                class="date-range-input pl-9 pr-3 py-2 text-sm
                    border border-slate-200 rounded-xl
                    focus:outline-none focus:ring-2 focus:ring-[#0C2C6B]
                    bg-white text-slate-700 shadow-sm cursor-pointer
                    w-full text-center font-medium"
                placeholder="Select date range"
            >
        </div>

        {{-- CLEAR DATE --}}
        @if ($dateRange)
            <button
                type="button"
                wire:click="clearDateFilter"
                title="Clear date filter"
                class="inline-flex items-center justify-center px-3 py-2 text-sm
                    text-slate-500 hover:text-red-500 hover:border-red-200
                    hover:bg-red-50 rounded-xl border border-slate-200
                    bg-white shadow-sm cursor-pointer transition"
            >
                ✕
            </button>
        @endif

        {{-- REFRESH BUTTON --}}
        <button
            type="button"
            wire:click="resetTable"
            wire:loading.attr="disabled"
            title="Refresh Table"
            class="refresh-btn"
        >
            <span wire:loading.remove wire:target="resetTable" class="refresh-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M4 4v5h5M20 20v-5h-5M5.5 9A7 7 0 0 1 18 6.5L20 9M18.5 15A7 7 0 0 1 6 17.5L4 15"
                    />
                </svg>
            </span>

            <span wire:loading wire:target="resetTable" class="refresh-loading">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="9" stroke-width="3" class="refresh-circle" />
                    <path
                        d="M21 12a9 9 0 0 1-9 9"
                        stroke-width="3"
                        stroke-linecap="round"
                    />
                </svg>
            </span>

            <span>Refresh</span>
        </button>

    </div>
</div>


    {{-- Branch table --}}
    <div class="overflow-x-auto rounded-xl border border-slate-200 bg-white shadow-sm">
        <table class="w-full border-collapse text-left">
            <thead class="bg-[#0C2C6B] text-xs uppercase tracking-wider text-white">
                <tr>
                    <th class="px-4 py-3">#</th>
                    <th class="px-4 py-3">Branch Name</th>
                    <th class="px-4 py-3">Address</th>
                    <th class="px-4 py-3">Status</th>
                    @if ($this->canUpdate || $this->canDelete)
                        <th class="px-4 py-3 text-center">Actions</th>
                    @endif
                </tr>
            </thead>

            <tbody class="divide-y divide-slate-200 text-sm">
                @forelse ($branches as $index => $branch)
                    <tr wire:key="branch-{{ $branch->BranchID }}"
                        class="transition hover:bg-slate-50">

                        <td class="px-4 py-4 text-slate-500">
                            {{ $branches->firstItem() + $index }}
                        </td>

                        <td class="px-4 py-4 font-semibold text-slate-900">
                            {{ $branch->branchname }}
                        </td>

                        <td class="max-w-xs truncate px-4 py-4 text-slate-500">
                            {{ $branch->address ?: 'No address provided.' }}
                        </td>

                        <td class="px-4 py-4">
                            @if ((bool) $branch->status)
                                <span class="inline-flex rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">
                                    Active
                                </span>
                            @else
                                <span class="inline-flex rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700">
                                    Inactive
                                </span>
                            @endif
                        </td>


                        {{-- Actions Column --}}
                        @if ($this->canUpdate || $this->canDelete)
                            <td class="px-4 py-4 text-center">
                                <div class="flex justify-center gap-2">

                                    {{-- EDIT BUTTON --}}
                                    @if ($this->canUpdate)
                                        <button
                                            type="button"
                                            wire:click="editBranch({{ $branch->BranchID }})"
                                            title="Update Branch"
                                            class="inline-flex items-center justify-center rounded-lg bg-[#0C2C6B] px-3 py-2 text-xs font-semibold text-white shadow transition hover:bg-[#09204f]">
                                            ✏️
                                        </button>
                                    @endif

                                    {{-- DELETE BUTTON --}}
                                    @if ($this->canDelete)
                                        <button
                                            type="button"
                                            wire:click="confirmDeleteBranch({{ $branch->BranchID }})"
                                            title="Delete Branch"
                                            class="inline-flex items-center justify-center rounded-lg border border-amber-400 bg-amber-500 px-3 py-2 text-xs font-semibold text-white shadow transition hover:bg-amber-600">
                                            🗑️
                                        </button>
                                    @endif

                                </div>
                            </td>
                        @endif

                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ ($this->canUpdate || $this->canDelete) ? 5 : 4 }}"
                            class="px-4 py-10 text-center text-slate-500">
                            No branches found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Pagination --}}
        @if ($branches->hasPages())
            <div class="flex flex-wrap items-center justify-center gap-4 border-t border-slate-200 bg-slate-50 px-4 py-4">
                <button type="button" wire:click="previousPage"
                    wire:loading.attr="disabled"
                    @disabled($branches->onFirstPage())
                    class="rounded-lg bg-[#D79B10] px-4 py-2 text-sm font-semibold text-white disabled:cursor-not-allowed disabled:opacity-40">
                    Previous
                </button>

                <span class="text-sm text-slate-600">
                    Page {{ $branches->currentPage() }} of {{ $branches->lastPage() }}
                </span>

                <button type="button" wire:click="nextPage"
                    wire:loading.attr="disabled"
                    @disabled(!$branches->hasMorePages())
                    class="rounded-lg bg-[#0C2C6B] px-4 py-2 text-sm font-semibold text-white disabled:cursor-not-allowed disabled:opacity-40">
                    Next
                </button>
            </div>
        @endif
    </div>

    {{-- Add/Edit modal --}}
    @if ($isOpen)
        <div class="fixed inset-0 z-[9999] flex items-center justify-center overflow-y-auto bg-slate-950/70 p-4 backdrop-blur-sm">
            <div class="my-8 w-full max-w-lg overflow-hidden rounded-2xl bg-white shadow-2xl">

                <div class="flex items-center justify-between bg-[#0C2C6B] px-6 py-4 text-white">
                    <div>
                        <h3 class="text-lg font-bold">
                            {{ $branch_id ? 'Edit Branch' : 'Add New Branch' }}
                        </h3>
                        <p class="text-xs text-blue-100">Enter the branch details below.</p>
                    </div>

                    <button type="button" wire:click="closeModal"
                        class="rounded-full bg-white/20 px-3 py-1.5 hover:bg-white/30">
                        ✕
                    </button>
                </div>

                <form wire:submit="saveBranch">
                    <div class="max-h-[75vh] space-y-4 overflow-y-auto p-6">

                        {{-- Branch name --}}
                        <div>
                            <label for="branchname"
                                class="mb-1 block text-xs font-bold uppercase tracking-wider text-slate-500">
                                Branch Name
                            </label>

                            <input id="branchname" type="text" wire:model="branchname"
                                required maxlength="150"
                                class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0C2C6B]">

                            @error('branchname')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Address --}}
                        <div>
                            <label for="address"
                                class="mb-1 block text-xs font-bold uppercase tracking-wider text-slate-500">
                                Address
                            </label>

                            <textarea id="address" wire:model="address" required rows="3"
                                class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0C2C6B]"></textarea>

                            @error('address')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Status --}}
                        <div>
                            <label for="branch-status"
                                class="mb-1 block text-xs font-bold uppercase tracking-wider text-slate-500">
                                Branch Status
                            </label>

                            <select
                                id="branch-status"
                                wire:model="status"
                                class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0C2C6B]"
                            >
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>

                            @error('status')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 border-t border-slate-200 bg-slate-50 px-6 py-4">
                        <button type="button" wire:click="closeModal"
                            class="rounded-lg bg-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-300">
                            Cancel
                        </button>

                        <button type="submit"
                            wire:loading.attr="disabled"
                            wire:target="saveBranch"
                            class="rounded-lg bg-[#0C2C6B] px-4 py-2 text-sm font-semibold text-white hover:bg-[#09204f] disabled:opacity-50">
                            <span wire:loading.remove wire:target="saveBranch">
                                {{ $branch_id ? 'Update Branch' : 'Save Branch' }}
                            </span>
                            <span wire:loading wire:target="saveBranch">
                                Saving...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- JavaScript: date picker and delete confirmation --}}
    @script
        <script>
            const dateInput = $wire.$el.querySelector('#branchDateRange');

            if (dateInput && typeof flatpickr !== 'undefined') {
                const picker = flatpickr(dateInput, {
                    mode: 'range',
                    dateFormat: 'Y-m-d',
                    altInput: true,
                    altFormat: 'M d, Y',
                    maxDate: 'today',
                    defaultDate: @js($dateRange ? explode(' to ', $dateRange) : []),

                    onChange(selectedDates, dateStr, instance) {
                        if (selectedDates.length === 2) {
                            const start = instance.formatDate(selectedDates[0], 'Y-m-d');
                            const end = instance.formatDate(selectedDates[1], 'Y-m-d');
                            $wire.set('dateRange', `${start} to ${end}`);
                        } else if (selectedDates.length === 0) {
                            $wire.set('dateRange', '');
                        }
                    },

                    onClose(selectedDates, dateStr, instance) {
                        if (selectedDates.length === 1) {
                            const day = instance.formatDate(selectedDates[0], 'Y-m-d');
                            instance.setDate([selectedDates[0], selectedDates[0]], false);
                            $wire.set('dateRange', `${day} to ${day}`);
                        }
                    }
                });

                $wire.on('ranges-reset', () => picker.clear());
            }

            $wire.on('confirm-delete', (event) => {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#0C2C6B',
                    cancelButtonColor: '#D79B10',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $wire.deleteBranch(event.id);
                    }
                });
            });

            $wire.on('branch-deleted', () => {
                Swal.fire({
                    title: 'Deleted!',
                    text: 'Branch has been successfully deleted.',
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: false
                });
            });
        </script>
    @endscript

</div>
