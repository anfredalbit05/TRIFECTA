
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

    {{-- Search and filters --}}
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center">
        <input type="text"
            wire:model.live.debounce.300ms="search"
            placeholder="Search branches..."
            class="w-full flex-1 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm focus:border-[#0C2C6B] focus:outline-none focus:ring-2 focus:ring-[#0C2C6B] sm:max-w-md">

        <div wire:ignore class="w-full sm:w-72">
            <input type="text" id="branchDateRange" readonly
                placeholder="Select date range"
                class="w-full cursor-pointer rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-center text-sm text-slate-700">
        </div>

        @if ($dateRange)
            <button type="button" wire:click="clearDateFilter"
                class="rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-600 hover:bg-red-50 hover:text-red-600">
                Clear Date
            </button>
        @endif

        <button type="button" wire:click="resetTable"
            wire:loading.attr="disabled"
            class="rounded-xl bg-slate-100 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-200 disabled:opacity-50">
            Refresh
        </button>
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
