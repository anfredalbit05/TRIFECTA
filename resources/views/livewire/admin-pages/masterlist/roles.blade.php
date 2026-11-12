
<div class="branches-container w-full px-4 sm:px-6 lg:px-8 py-6">

    {{-- Header --}}
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">
                Roles
            </h2>
            <p class="mt-1 text-sm text-slate-500">
                Manage system roles and their descriptions.
            </p>
        </div>

        <button
            type="button"
            wire:click="openCreateModal"
            class="rounded-xl bg-[#0C2C6B] px-4 py-2.5 text-sm font-semibold text-white shadow transition hover:bg-[#09204f]"
        >
            + Add Role
        </button>
    </div>

    {{-- Flash Message --}}
    @if (session()->has('message'))
        <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-700">
            {{ session('message') }}
        </div>
    @endif

    {{-- Search, Date Range and Refresh --}}
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">

        {{-- Search --}}
        <div class="relative w-full max-w-sm flex-1 sm:w-auto">

            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
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
                placeholder="Search roles..."
                class="w-full rounded-xl border border-slate-200 bg-white py-2.5 pl-10 pr-4 text-sm shadow-sm transition focus:border-[#0C2C6B] focus:outline-none focus:ring-2 focus:ring-[#0C2C6B]"
            >
        </div>

        {{-- Filters --}}
        <div class="flex w-full flex-wrap items-center justify-end gap-3 sm:w-auto">

            {{-- Date Range --}}
            <div wire:ignore class="relative w-full sm:w-72">

                <span class="pointer-events-none absolute inset-y-0 left-3 z-10 flex items-center text-slate-400">
                    📅
                </span>

                <input
                    type="text"
                    id="roleDateRange"
                    readonly
                    class="date-range-input w-full cursor-pointer rounded-xl border border-slate-200 bg-white py-2 pl-9 pr-3 text-center text-sm font-medium text-slate-700 shadow-sm focus:outline-none focus:ring-2 focus:ring-[#0C2C6B]"
                    placeholder="Select date range"
                >
            </div>

            {{-- Clear Date --}}
            @if ($dateRange)
                <button
                    type="button"
                    wire:click="clearDateFilter"
                    title="Clear date filter"
                    class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-500 shadow-sm transition hover:border-red-200 hover:bg-red-50 hover:text-red-500"
                >
                    ✕
                </button>
            @endif

            {{-- Refresh --}}
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

    {{-- Roles Table --}}
    <div class="overflow-x-auto rounded-xl border border-slate-200 bg-white shadow-sm">

        <table class="w-full border-collapse text-left">

            <thead class="bg-[#0C2C6B] text-xs uppercase tracking-wider text-white">
                <tr>
                    <th class="px-4 py-3">#</th>
                    <th class="px-4 py-3">Role Name</th>
                    <th class="px-4 py-3">Description</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3 text-center">Actions</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-slate-200 text-sm">

                @forelse ($roles as $index => $role)

                    <tr
                        wire:key="role-{{ $role->RoleID }}"
                        class="transition hover:bg-slate-50"
                    >

                        {{-- Number --}}
                        <td class="px-4 py-4 text-slate-500">
                            {{ $roles->firstItem() + $index }}
                        </td>

                        {{-- Role Name --}}
                        <td class="px-4 py-4 font-semibold text-slate-900">
                            {{ $role->rolename }}
                        </td>

                        {{-- Description --}}
                        <td class="max-w-md px-4 py-4 text-slate-500">
                            <span class="block max-w-md truncate">
                                {{ $role->description ?: 'No description provided.' }}
                            </span>
                        </td>

                        {{-- Status --}}
                        <td class="px-4 py-4">

                            @if ((bool) $role->status)

                                <span class="inline-flex rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">
                                    Active
                                </span>

                            @else

                                <span class="inline-flex rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700">
                                    Inactive
                                </span>

                            @endif

                        </td>

                        {{-- Actions --}}
                        <td class="px-4 py-4 text-center">

                            <div class="flex justify-center gap-2">

                                {{-- Edit --}}
                                <button
                                    type="button"
                                    wire:click="editRole({{ $role->RoleID }})"
                                    title="Update Role"
                                    class="inline-flex items-center justify-center rounded-lg bg-[#0C2C6B] px-3 py-2 text-xs font-semibold text-white shadow transition hover:bg-[#09204f]"
                                >
                                    ✏️
                                </button>

                                {{-- Delete --}}
                                <button
                                    type="button"
                                    wire:click="confirmDeleteRole({{ $role->RoleID }})"
                                    title="Delete Role"
                                    class="inline-flex items-center justify-center rounded-lg border border-amber-400 bg-amber-500 px-3 py-2 text-xs font-semibold text-white shadow transition hover:bg-amber-600"
                                >
                                    🗑️
                                </button>

                            </div>

                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="5" class="px-4 py-10 text-center text-slate-500">
                            No roles found.
                        </td>
                    </tr>

                @endforelse

            </tbody>

        </table>

        {{-- Pagination --}}
        @if ($roles->hasPages())

            <div class="flex flex-wrap items-center justify-center gap-4 border-t border-slate-200 bg-slate-50 px-4 py-4">

                <button
                    type="button"
                    wire:click="previousPage"
                    wire:loading.attr="disabled"
                    @disabled($roles->onFirstPage())
                    class="rounded-lg bg-[#D79B10] px-4 py-2 text-sm font-semibold text-white disabled:cursor-not-allowed disabled:opacity-40"
                >
                    Previous
                </button>

                <span class="text-sm text-slate-600">
                    Page {{ $roles->currentPage() }} of {{ $roles->lastPage() }}
                </span>

                <button
                    type="button"
                    wire:click="nextPage"
                    wire:loading.attr="disabled"
                    @disabled(!$roles->hasMorePages())
                    class="rounded-lg bg-[#0C2C6B] px-4 py-2 text-sm font-semibold text-white disabled:cursor-not-allowed disabled:opacity-40"
                >
                    Next
                </button>

            </div>

        @endif

    </div>

    {{-- Add / Edit Role Modal --}}
    @if ($isOpen)

        <div class="fixed inset-0 z-[9999] flex items-center justify-center overflow-y-auto bg-slate-950/70 p-4 backdrop-blur-sm">

            <div class="my-8 w-full max-w-lg overflow-hidden rounded-2xl bg-white shadow-2xl">

                {{-- Modal Header --}}
                <div class="flex items-center justify-between bg-[#0C2C6B] px-6 py-4 text-white">

                    <div>
                        <h3 class="text-lg font-bold">
                            {{ $role_id ? 'Edit Role' : 'Add New Role' }}
                        </h3>

                        <p class="text-xs text-blue-100">
                            Enter the role details below.
                        </p>
                    </div>

                    <button
                        type="button"
                        wire:click="closeModal"
                        class="rounded-full bg-white/20 px-3 py-1.5 transition hover:bg-white/30"
                    >
                        ✕
                    </button>

                </div>

                {{-- Form --}}
                <form wire:submit="saveRole">

                    <div class="max-h-[75vh] space-y-4 overflow-y-auto p-6">

                        {{-- Role Name --}}
                        <div>

                            <label
                                for="rolename"
                                class="mb-1 block text-xs font-bold uppercase tracking-wider text-slate-500"
                            >
                                Role Name <span class="text-red-500">*</span>
                            </label>

                            <input
                                id="rolename"
                                type="text"
                                wire:model="rolename"
                                required
                                maxlength="100"
                                placeholder="Enter role name"
                                class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-[#0C2C6B] focus:outline-none focus:ring-2 focus:ring-[#0C2C6B]"
                            >

                            @error('rolename')
                                <p class="mt-1 text-xs text-red-500">
                                    {{ $message }}
                                </p>
                            @enderror

                        </div>

                        {{-- Description --}}
                        <div>

                            <label
                                for="description"
                                class="mb-1 block text-xs font-bold uppercase tracking-wider text-slate-500"
                            >
                                Description
                            </label>

                            <textarea
                                id="description"
                                wire:model="description"
                                rows="4"
                                placeholder="Enter role description (optional)"
                                class="w-full resize-y rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-[#0C2C6B] focus:outline-none focus:ring-2 focus:ring-[#0C2C6B]"
                            ></textarea>

                            @error('description')
                                <p class="mt-1 text-xs text-red-500">
                                    {{ $message }}
                                </p>
                            @enderror

                        </div>

                        {{-- Status --}}
                        <div>

                            <label
                                for="role-status"
                                class="mb-1 block text-xs font-bold uppercase tracking-wider text-slate-500"
                            >
                                Role Status
                            </label>

                            <select
                                id="role-status"
                                wire:model="status"
                                class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm focus:border-[#0C2C6B] focus:outline-none focus:ring-2 focus:ring-[#0C2C6B]"
                            >
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>

                            @error('status')
                                <p class="mt-1 text-xs text-red-500">
                                    {{ $message }}
                                </p>
                            @enderror

                        </div>

                    </div>

                    {{-- Modal Footer --}}
                    <div class="flex justify-end gap-2 border-t border-slate-200 bg-slate-50 px-6 py-4">

                        <button
                            type="button"
                            wire:click="closeModal"
                            class="rounded-lg bg-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-300"
                        >
                            Cancel
                        </button>

                        <button
                            type="submit"
                            wire:loading.attr="disabled"
                            wire:target="saveRole"
                            class="rounded-lg bg-[#0C2C6B] px-4 py-2 text-sm font-semibold text-white transition hover:bg-[#09204f] disabled:opacity-50"
                        >

                            <span wire:loading.remove wire:target="saveRole">
                                {{ $role_id ? 'Update Role' : 'Save Role' }}
                            </span>

                            <span wire:loading wire:target="saveRole">
                                Saving...
                            </span>

                        </button>

                    </div>

                </form>

            </div>

        </div>

    @endif

    {{-- JavaScript --}}
    @script
        <script>

            // Date Range Picker
            const dateInput = $wire.$el.querySelector('#roleDateRange');

            if (dateInput && typeof flatpickr !== 'undefined') {

                const picker = flatpickr(dateInput, {

                    mode: 'range',
                    dateFormat: 'Y-m-d',

                    altInput: true,
                    altFormat: 'M d, Y',

                    maxDate: 'today',

                    defaultDate: @js(
                        $dateRange
                            ? explode(' to ', $dateRange)
                            : []
                    ),

                    onChange(selectedDates, dateStr, instance) {

                        if (selectedDates.length === 2) {

                            const start = instance.formatDate(
                                selectedDates[0],
                                'Y-m-d'
                            );

                            const end = instance.formatDate(
                                selectedDates[1],
                                'Y-m-d'
                            );

                            $wire.set(
                                'dateRange',
                                `${start} to ${end}`
                            );

                        } else if (selectedDates.length === 0) {

                            $wire.set('dateRange', '');

                        }

                    },

                    onClose(selectedDates, dateStr, instance) {

                        if (selectedDates.length === 1) {

                            const day = instance.formatDate(
                                selectedDates[0],
                                'Y-m-d'
                            );

                            instance.setDate(
                                [selectedDates[0], selectedDates[0]],
                                false
                            );

                            $wire.set(
                                'dateRange',
                                `${day} to ${day}`
                            );

                        }

                    }

                });

                // Reset date picker
                $wire.on('ranges-reset', () => {
                    picker.clear();
                });

            }

            // Delete Confirmation
            $wire.on('confirm-delete', (event) => {

                Swal.fire({

                    title: 'Are you sure?',

                    text: "You won't be able to revert this!",

                    icon: 'warning',

                    showCancelButton: true,

                    confirmButtonColor: '#0C2C6B',

                    cancelButtonColor: '#D79B10',

                    confirmButtonText: 'Yes, delete it!',

                    cancelButtonText: 'Cancel',

                }).then((result) => {

                    if (result.isConfirmed) {

                        $wire.deleteRole(event.id);

                    }

                });

            });

            // Successfully Deleted
            $wire.on('role-deleted', () => {

                Swal.fire({

                    title: 'Deleted!',

                    text: 'Role has been successfully deleted.',

                    icon: 'success',

                    timer: 1500,

                    showConfirmButton: false

                });

            });

        </script>
    @endscript

</div>
