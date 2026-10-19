<div class="benefits-container w-full px-4 sm:px-6 lg:px-8 py-6">

    <!-- HEADER & BENEFIT TOGGLE BUTTON -->
    <div class="members-header flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="masterlist-title text-2xl font-bold text-slate-800">Benefits</h2>
            <p class="masterlist-description text-sm text-slate-500 mt-0.5">Available member incentives and rewards.</p>
        </div>

        @if ($this->canCreate)
            <button
                type="button"
                wire:click="openCreateModal"
                class="masterlist-btn inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold transition cursor-pointer shadow bg-[#0C2C6B] text-white hover:bg-[#09204f] w-full sm:w-auto"
            >
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Add Benefit
            </button>
        @endif
    </div>

    <!-- Success Message Notification -->
    @if (session()->has('message'))
        <div class="p-4 mb-4 text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-xl text-sm font-medium">
            {{ session('message') }}
        </div>
    @endif

    <!-- SEARCH, VARIANT FILTER & REFRESH -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">

        <!-- SEARCH AND VARIANT FILTER -->
        <div class="flex flex-col sm:flex-row items-center gap-3 w-full lg:max-w-3xl">

            <!-- SEARCH INPUT -->
            <div class="relative w-full sm:flex-1">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400 pointer-events-none">
                    <svg
                        class="h-4 w-4"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
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
                    placeholder="Search benefits..."
                    class="w-full pl-10 pr-4 py-2.5 text-sm border border-slate-200 rounded-xl
                        focus:outline-none focus:ring-2 focus:ring-[#0C2C6B]
                        focus:border-[#0C2C6B] bg-white shadow-sm transition"
                >
            </div>

            <!-- VARIANT FILTER -->
            <div class="w-full sm:w-56 sm:flex-shrink-0">
                <select
                    wire:model.live="selectedVariant"
                    class="w-full px-3 py-2.5 text-sm border border-slate-200 rounded-xl
                        focus:outline-none focus:ring-2 focus:ring-[#0C2C6B]
                        focus:border-[#0C2C6B] bg-white shadow-sm transition"
                >
                    <option value="">All Variants</option>

                    @foreach ($variants as $variant)
                        <option value="{{ $variant->VID }}">
                            {{ $variant->name }}
                        </option>
                    @endforeach
                </select>
            </div>

        </div>

        <!-- REFRESH BUTTON -->
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
                    <circle
                        cx="12"
                        cy="12"
                        r="9"
                        stroke-width="3"
                        class="refresh-circle"
                    />
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

    <!-- BENEFITS TABLE -->
    <div class="overflow-x-auto bg-white rounded-xl border border-slate-200 shadow-sm">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-slate-200 bg-[#0C2C6B] text-xs font-semibold uppercase tracking-wider text-white">
                    <th class="py-3 px-4">#</th>
                    <th class="py-3 px-4">Benefit Name</th>
                    <th class="py-3 px-4">Details</th>
                    <th class="py-3">Variant</th>
                    <th class="py-3 px-4">Holder Type</th>
                    <th class="py-3 px-4">Amount</th>
                     @if($this->canUpdate || $this->canDelete || $this->canRead || $this->canCreate)
                        <th class="py-3 px-4 text-center">Action</th>
                    @endif
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 text-sm text-slate-700">
                @forelse($benefits as $index => $benefit)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="py-4 px-4 font-medium text-slate-500">
                            {{ $benefits->firstItem() + $index }}
                        </td>

                        <td class="py-4 px-4 font-semibold text-slate-900">
                            {{ $benefit->name }}
                        </td>

                        <td class="py-4 px-4 text-slate-500 max-w-xs truncate">
                            {{ $benefit->details ?? 'No details provided.' }}
                        </td>

                        <td class="py-4 px-4">
                            <span class="inline-flex items-center px-3 py-1 rounded-md
                                        bg-blue-50 text-blue-800 text-xs font-semibold">
                                {{ $benefit->variant_name }}
                            </span>
                        </td>

                        <td class="py-4 px-4">
                            <span class="inline-flex justify-center items-center px-3 py-1 rounded-md bg-slate-100 text-slate-700 text-xs font-semibold">
                                {{ $benefit->holdertype }}
                            </span>
                        </td>

                        <td class="py-4 px-4 font-semibold text-slate-900">
                            ₱{{ number_format($benefit->amount, 2) }}
                        </td>

                        <!-- Actions Column -->
                        @if($this->canUpdate || $this->canDelete || $this->canRead || $this->canCreate)
                            <td class="py-4 px-4 text-center">
                                <div class="flex gap-2 justify-center">
                                    {{-- EDIT BUTTON --}}
                                    @if($this->canUpdate)
                                        <button
                                            wire:click="editBenefit({{ $benefit->BentID }})"
                                            title="Update Benefit"
                                            class="inline-flex items-center justify-center px-3 py-2 rounded-lg bg-[#0C2C6B] text-white hover:bg-[#09204f] transition cursor-pointer text-xs font-semibold shadow">
                                            ✏️
                                        </button>
                                    @endif

                                    {{-- DELETE BUTTON --}}
                                    @if($this->canDelete)
                                        <button
                                            wire:click="confirmDeleteBenefit({{ $benefit->BentID }})"
                                            title="Delete Benefit"
                                            class="inline-flex items-center justify-center px-3 py-2 rounded-lg border-amber-400 bg-amber-500 text-white hover:bg-amber-500 transition cursor-pointer text-xs font-semibold shadow">
                                            🗑️
                                        </button>
                                    @endif
                                </div>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-8 text-slate-500">
                            No active benefits available at the moment.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    <!-- PAGINATION -->
    @if($benefits->hasPages())
        <div class="px-4 py-4 border-t border-slate-200 bg-slate-50 flex justify-center items-center space-x-4">

            {{-- Previous Page Link --}}
            @if ($benefits->onFirstPage())
                <span class="px-4 py-2 bg-gray-200 text-gray-400 rounded cursor-not-allowed">
                    Previous
                </span>
            @else
                <button
                    wire:click="previousPage"
                    wire:loading.attr="disabled"
                    class="px-4 py-2 text-white rounded transition cursor-pointer"
                    style="background-color: #D79B10;"
                    onmouseover="this.style.backgroundColor='#c58d0f'"
                    onmouseout="this.style.backgroundColor='#D79B10'"
                >
                    Previous
                </button>
            @endif

            <span class="text-sm text-gray-600">
                Page {{ $benefits->currentPage() }} of {{ $benefits->lastPage() }}
            </span>

            {{-- Next Page Link --}}
            @if ($benefits->hasMorePages())
                <button
                    wire:click="nextPage"
                    wire:loading.attr="disabled"
                    class="px-4 py-2 text-white rounded transition cursor-pointer"
                    style="background-color: #0C2C6B;"
                    onmouseover="this.style.backgroundColor='#0a2559'"
                    onmouseout="this.style.backgroundColor='#0C2C6B'"
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

    <!-- ADD/EDIT BENEFIT MODAL -->
    @if($isOpen)
        <div class="fixed inset-0 z-[9999] flex items-center justify-center bg-slate-950/70 p-4 backdrop-blur-sm overflow-y-auto">
            <div class="w-full max-w-lg overflow-hidden rounded-2xl bg-white shadow-2xl my-8">

                <!-- MODAL HEADER -->
                <div class="bg-[#0C2C6B] px-6 py-4 text-white flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-bold">
                            {{ $benefit_id ? 'Edit Benefit' : 'Add New Benefit' }}
                        </h3>
                        <p class="text-xs text-blue-100">Fill in the benefit details below.</p>
                    </div>
                    <button
                        wire:click="closeModal"
                        class="flex h-8 w-8 items-center justify-center rounded-full bg-white/20 transition hover:bg-white/30 text-white font-bold cursor-pointer"
                    >
                        ✕
                    </button>
                </div>

                <form wire:submit.prevent="saveBenefit">
                    <!-- MODAL BODY -->
                    <div class="p-6 space-y-4 max-h-[75vh] overflow-y-auto">



                        <!-- Variant -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">
                                Variant
                            </label>

                            <select
                                wire:model="benefitVariantID"
                                class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg
                                    focus:outline-none focus:ring-2 focus:ring-[#0C2C6B] bg-white"
                            >
                                <option value="">Select Variant</option>

                                @foreach ($variants as $variant)
                                    <option value="{{ $variant->VID }}">
                                        {{ $variant->name }}
                                    </option>
                                @endforeach
                            </select>

                            @error('benefitVariantID')
                                <span class="text-xs text-red-500 mt-1 block">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <!-- Name -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">Benefit Name</label>
                            <input type="text" wire:model="name" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0C2C6B]">
                            @error('name') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>


                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">Amount (₱)</label>
                            <input type="number" step="0.01" wire:model="amount" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0C2C6B]">
                            @error('amount') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Holder Type -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">Holder Type</label>
                            <select wire:model="holdertype" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0C2C6B] bg-white">
                                <option value="Both">Both</option>
                                <option value="Primary Owner">Primary Owner</option>
                                <option value="Beneficiaries">Beneficiaries</option>
                            </select>
                            @error('holdertype') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Details -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">Details</label>
                            <textarea wire:model="details" class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0C2C6B]" rows="3"></textarea>
                            @error('details') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                    </div>

                    <!-- MODAL FOOTER -->
                    <div class="flex justify-end gap-2 border-t border-slate-200 px-6 py-3 bg-slate-50">
                        <button
                            type="button"
                            wire:click="closeModal"
                            class="px-4 py-2 text-xs font-semibold text-slate-700 bg-slate-200 rounded-lg hover:bg-slate-300 transition cursor-pointer">
                            Cancel
                        </button>
                        <button
                            type="submit"
                            class="px-4 py-2 text-xs font-semibold text-white bg-[#0C2C6B] rounded-lg hover:bg-[#09204f] transition cursor-pointer shadow">
                            Save Benefit
                        </button>
                    </div>
                </form>

            </div>
        </div>
    @endif

    @script
    <script>
        $wire.on('confirm-delete', (event) => {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#0B2A5B',
                cancelButtonColor: '#D79B10',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $wire.deleteBenefit(event.id);
                }
            });
        });

        $wire.on('benefit-deleted', () => {
            Swal.fire({
                title: 'Deleted!',
                text: 'Benefit has been successfully deleted.',
                icon: 'success',
                timer: 1500,
                showConfirmButton: false
            });
        });
    </script>
    @endscript

</div>
