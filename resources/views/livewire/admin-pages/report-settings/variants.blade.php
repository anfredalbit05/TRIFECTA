<div class="benefits-container w-full px-4 sm:px-6 lg:px-8 py-6">

    {{-- HEADER --}}
    <div class="members-header flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="masterlist-title text-2xl font-bold text-slate-800">
                Variants
            </h2>
            <p class="masterlist-description text-sm text-slate-500 mt-0.5">
                Manage membership variants and their availability.
            </p>
        </div>

        @if ($this->canCreate)
            <button
                type="button"
                wire:click="openCreateModal"
                class="masterlist-btn inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold transition cursor-pointer shadow bg-[#0C2C6B] text-white hover:bg-[#09204f] w-full sm:w-auto"
            >
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Add Variant
            </button>
        @endif
    </div>

    {{-- SUCCESS MESSAGE --}}
    @if (session()->has('success'))
        <div class="p-4 mb-4 text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-xl text-sm font-medium">
            {{ session('success') }}
        </div>
    @endif

    {{-- ERROR MESSAGE --}}
    @if (session()->has('error'))
        <div class="p-4 mb-4 text-red-700 bg-red-50 border border-red-200 rounded-xl text-sm font-medium">
            {{ session('error') }}
        </div>
    @endif

    {{-- VALIDATION ERRORS --}}
    @if ($errors->any())
        <div class="p-4 mb-4 text-red-700 bg-red-50 border border-red-200 rounded-xl text-sm">
            <p class="font-semibold mb-1">Please check the following:</p>
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- SEARCH AND REFRESH --}}
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">

        <div class="relative w-full lg:max-w-3xl">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400 pointer-events-none">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="m21 21-4.35-4.35m2.35-5.65a8 8 0 1 1-16 0 8 8 0 0 1 16 0Z"/>
                </svg>
            </span>

            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Search variants..."
                class="w-full pl-10 pr-4 py-2.5 text-sm border border-slate-200 rounded-xl
                    focus:outline-none focus:ring-2 focus:ring-[#0C2C6B]
                    focus:border-[#0C2C6B] bg-white shadow-sm transition"
            >
        </div>

        <div class="flex items-center justify-between lg:justify-end gap-4">
            <span class="text-sm text-slate-500 whitespace-nowrap">
                {{ $variants->total() }} variant(s)
            </span>

            <button
                type="button"
                wire:click="resetTable"
                wire:loading.attr="disabled"
                wire:target="resetTable"
                title="Refresh Table"
                class="refresh-btn self-start lg:self-auto flex-shrink-0"
            >
                <span wire:loading.remove wire:target="resetTable" class="refresh-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 4v5h5M20 20v-5h-5M5.5 9A7 7 0 0 1 18 6.5L20 9M18.5 15A7 7 0 0 1 6 17.5L4 15"/>
                    </svg>
                </span>

                <span wire:loading wire:target="resetTable" class="refresh-loading">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="9" stroke-width="3" class="refresh-circle"/>
                        <path d="M21 12a9 9 0 0 1-9 9" stroke-width="3" stroke-linecap="round"/>
                    </svg>
                </span>

                <span>Refresh</span>
            </button>
        </div>
    </div>

    {{-- VARIANTS TABLE --}}
    <div class="overflow-x-auto bg-white rounded-xl border border-slate-200 shadow-sm">
        <table class="w-full min-w-[700px] text-left border-collapse">

            <thead>
                <tr class="border-b border-slate-200 bg-[#0C2C6B] text-xs font-semibold uppercase tracking-wider text-white">
                    <th class="py-3 px-4">#</th>
                    <th class="py-3 px-4">Variant Name</th>
                    <th class="py-3 px-4">Description</th>
                    <th class="py-3 px-4">Status</th>

                    @if ($this->canUpdate || $this->canDelete)
                        <th class="py-3 px-4 text-center">Action</th>
                    @endif
                </tr>
            </thead>

            <tbody class="divide-y divide-slate-200 text-sm text-slate-700">
                @forelse ($variants as $index => $variant)
                    <tr wire:key="variant-{{ $variant->VID }}" class="hover:bg-slate-50 transition-colors">

                        {{-- ROW NUMBER --}}
                        <td class="py-4 px-4 font-medium text-slate-500">
                            {{ $variants->firstItem() + $index }}
                        </td>

                        {{-- VARIANT NAME --}}
                        <td class="py-4 px-4 font-semibold text-slate-900">
                            {{ $variant->name }}
                        </td>

                        {{-- DESCRIPTION --}}
                        <td class="py-4 px-4 text-slate-500 max-w-sm">
                            <span class="block truncate" title="{{ $variant->description }}">
                                {{ $variant->description ?: 'No description provided.' }}
                            </span>
                        </td>

                        {{-- STATUS --}}
                        <td class="py-4 px-4">
                            @php
                                $status = strtolower(trim((string) $variant->status));
                                $isActive = in_array($status, ['active', '1', 'true', 'enabled'], true);
                            @endphp

                            <span @class([
                                'inline-flex items-center px-3 py-1 rounded-md text-xs font-semibold',
                                'bg-emerald-50 text-emerald-700' => $isActive,
                                'bg-slate-100 text-slate-600' => ! $isActive,
                            ])>
                                {{ $variant->status ?: 'Unknown' }}
                            </span>
                        </td>

                        {{-- ACTIONS --}}
                        @if ($this->canUpdate || $this->canDelete)
                            <td class="py-4 px-4 text-center">
                                <div class="flex gap-2 justify-center">

                                    @if ($this->canUpdate)
                                        <button
                                            type="button"
                                            wire:click="editVariant({{ $variant->VID }})"
                                            title="Update Variant"
                                            class="inline-flex items-center justify-center px-3 py-2 rounded-lg bg-[#0C2C6B] text-white hover:bg-[#09204f] transition cursor-pointer text-xs font-semibold shadow"
                                        >
                                            ✏️ Edit
                                        </button>
                                    @endif

                                    {{-- @if ($this->canDelete)
                                        <button
                                            type="button"
                                            wire:click="confirmDelete({{ $variant->VID }})"
                                            title="Delete Variant"
                                            class="inline-flex items-center justify-center px-3 py-2 rounded-lg bg-amber-500 text-white hover:bg-amber-600 transition cursor-pointer text-xs font-semibold shadow"
                                        >
                                            🗑️
                                        </button>
                                    @endif --}}

                                </div>
                            </td>
                        @endif
                    </tr>

                @empty
                    <tr>
                        <td
                            colspan="{{ ($this->canUpdate || $this->canDelete) ? 5 : 4 }}"
                            class="text-center py-10 px-4 text-slate-500"
                        >
                            <p class="font-semibold text-slate-700">No variants found.</p>
                            <p class="mt-1 text-sm">
                                Try another search or add a new variant.
                            </p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- PAGINATION --}}
        @if ($variants->hasPages())
            <div class="px-4 py-4 border-t border-slate-200 bg-slate-50 flex flex-wrap justify-center items-center gap-4">

                @if ($variants->onFirstPage())
                    <span class="px-4 py-2 bg-gray-200 text-gray-400 rounded cursor-not-allowed">
                        Previous
                    </span>
                @else
                    <button
                        type="button"
                        wire:click="previousPage"
                        wire:loading.attr="disabled"
                        class="px-4 py-2 text-white rounded transition cursor-pointer disabled:opacity-50"
                        style="background-color: #D79B10;"
                    >
                        Previous
                    </button>
                @endif

                <span class="text-sm text-gray-600">
                    Page {{ $variants->currentPage() }} of {{ $variants->lastPage() }}
                </span>

                @if ($variants->hasMorePages())
                    <button
                        type="button"
                        wire:click="nextPage"
                        wire:loading.attr="disabled"
                        class="px-4 py-2 text-white rounded transition cursor-pointer disabled:opacity-50"
                        style="background-color: #0C2C6B;"
                    >
                        Next
                    </button>
                @else
                    <span class="px-4 py-2 bg-gray-200 text-gray-400 rounded cursor-not-allowed">
                        Next
                    </span>
                @endif

            </div>
        @endif
    </div>

    {{-- ADD / EDIT VARIANT MODAL --}}
    @if ($isOpen)
        <div
            class="fixed inset-0 z-[9999] flex items-center justify-center bg-slate-950/70 p-4 backdrop-blur-sm overflow-y-auto"
            wire:key="variant-modal"
        >
            <div class="w-full max-w-lg overflow-hidden rounded-2xl bg-white shadow-2xl my-8">

                {{-- MODAL HEADER --}}
                <div class="bg-[#0C2C6B] px-6 py-4 text-white flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-bold">
                            {{ $variant_id ? 'Edit Variant' : 'Add New Variant' }}
                        </h3>
                        <p class="text-xs text-blue-100">
                            Fill in the variant details below.
                        </p>
                    </div>

                    <button
                        type="button"
                        wire:click="closeModal"
                        class="flex h-8 w-8 items-center justify-center rounded-full bg-white/20 transition hover:bg-white/30 text-white font-bold cursor-pointer"
                        aria-label="Close modal"
                    >
                        ✕
                    </button>
                </div>

                <form wire:submit.prevent="saveVariant">

                    {{-- MODAL BODY --}}
                    <div class="p-6 space-y-4 max-h-[75vh] overflow-y-auto">

                        {{-- NAME --}}
                        <div>
                            <label for="variant-name" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">
                                Variant Name <span class="text-red-500">*</span>
                            </label>

                            <input
                                id="variant-name"
                                type="text"
                                wire:model="name"
                                maxlength="100"
                                placeholder="Enter variant name"
                                class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0C2C6B]"
                            >

                            @error('name')
                                <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- DESCRIPTION --}}
                        <div>
                            <label for="variant-description" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">
                                Description
                            </label>

                            <textarea
                                id="variant-description"
                                wire:model="description"
                                rows="3"
                                maxlength="1000"
                                placeholder="Enter variant description"
                                class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0C2C6B]"
                            ></textarea>

                            @error('description')
                                <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- STATUS --}}
                        <div>
                            <label for="variant-status" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">
                                Status
                            </label>

                            <select
                                id="variant-status"
                                wire:model="status"
                                class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0C2C6B] bg-white"
                            >
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>

                            @error('status')
                                <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                    </div>

                    {{-- MODAL FOOTER --}}
                    <div class="flex justify-end gap-2 border-t border-slate-200 px-6 py-3 bg-slate-50">
                        <button
                            type="button"
                            wire:click="closeModal"
                            class="px-4 py-2 text-xs font-semibold text-slate-700 bg-slate-200 rounded-lg hover:bg-slate-300 transition cursor-pointer"
                        >
                            Cancel
                        </button>

                        <button
                            type="submit"
                            wire:loading.attr="disabled"
                            wire:target="saveVariant"
                            class="px-4 py-2 text-xs font-semibold text-white bg-[#0C2C6B] rounded-lg hover:bg-[#09204f] transition cursor-pointer shadow disabled:opacity-50"
                        >
                            <span wire:loading.remove wire:target="saveVariant">
                                {{ $variant_id ? 'Save Changes' : 'Create Variant' }}
                            </span>

                            <span wire:loading wire:target="saveVariant">
                                Saving...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- DELETE CONFIRMATION MODAL --}}
    @if ($confirmingDelete)
        <div class="fixed inset-0 z-[9999] flex items-center justify-center bg-slate-950/70 p-4 backdrop-blur-sm">
            <div class="w-full max-w-md overflow-hidden rounded-2xl bg-white shadow-2xl">

                <div class="bg-[#0C2C6B] px-6 py-4 text-white">
                    <h3 class="text-lg font-bold">Delete Variant</h3>
                    <p class="mt-1 text-xs text-blue-100">
                        Confirm before proceeding.
                    </p>
                </div>

                <div class="p-6">
                    <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-amber-100 text-xl text-amber-600">
                        !
                    </div>

                    <p class="text-sm leading-6 text-slate-600">
                        Are you sure you want to delete this variant? It cannot be deleted if it is still used by activations or membership fees.
                    </p>

                    <div class="mt-6 flex justify-end gap-2">
                        <button
                            type="button"
                            wire:click="cancelDelete"
                            class="px-4 py-2 text-xs font-semibold text-slate-700 bg-slate-200 rounded-lg hover:bg-slate-300 transition cursor-pointer"
                        >
                            Cancel
                        </button>

                        <button
                            type="button"
                            wire:click="deleteVariant"
                            wire:loading.attr="disabled"
                            wire:target="deleteVariant"
                            class="px-4 py-2 text-xs font-semibold text-white bg-amber-500 rounded-lg hover:bg-amber-600 transition cursor-pointer shadow disabled:opacity-50"
                        >
                            <span wire:loading.remove wire:target="deleteVariant">
                                Delete Variant
                            </span>

                            <span wire:loading wire:target="deleteVariant">
                                Deleting...
                            </span>
                        </button>
                    </div>
                </div>

            </div>
        </div>
    @endif

</div>
