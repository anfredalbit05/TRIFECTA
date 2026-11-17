<div class="faqs-container w-full px-4 sm:px-6 lg:px-8 py-6">

   <!-- HEADER & FAQ TOGGLE BUTTON -->
    <div class="members-header flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="masterlist-title text-2xl font-bold text-slate-800">FAQs</h2>
            <p class="masterlist-description text-sm text-slate-500 mt-0.5">Frequently asked questions and answers.</p>
        </div>

        @if ($this->canCreate)
            <button
                type="button"
                wire:click="openCreateModal"
                class="masterlist-btn inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold transition cursor-pointer shadow bg-[var(--color-primary)] text-white hover:bg-[var(--color-primary-dark)] w-full sm:w-auto"
            >
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Add FAQs
            </button>
        @endif
    </div>

    <!-- MESSAGE -->
    @if (session()->has('message'))
        <div class="p-4 mb-4 text-[var(--color-primary)] bg-[var(--color-primary-tint)] border border-[var(--color-primary-tint-border)] rounded-xl text-sm font-medium">
            {{ session('message') }}
        </div>
    @endif

    <!-- SEARCH INPUT & REFRESH BUTTON CONTAINER -->
    <div class="flex items-center justify-between gap-4 mb-6">
        <!-- SEARCH INPUT -->
<div class="relative max-w-sm w-full">

    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-500 pointer-events-none">
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
        placeholder="Search FAQs..."
        class="w-full pl-10 pr-4 py-2.5 text-sm border border-slate-200 rounded-xl
               focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)]
               focus:border-[var(--color-primary)]
               bg-white shadow-sm transition"
    >

</div>

        <!-- REFRESH BUTTON -->
            {{-- REFRESH --}}
            <button
                type="button"
                wire:click="resetTable"
                wire:loading.attr="disabled"
                title="Refresh Table"
                class="refresh-btn"
            >

                <span
                    wire:loading.remove
                    wire:target="resetTable"
                    class="refresh-icon"
                >
                    <svg
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M4 4v5h5M20 20v-5h-5M5.5 9A7 7 0 0 1 18 6.5L20 9M18.5 15A7 7 0 0 1 6 17.5L4 15"
                        />
                    </svg>
                </span>

                <span
                    wire:loading
                    wire:target="resetTable"
                    class="refresh-loading"
                >
                    <svg
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
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

    <!-- TABLE -->
    <div class="overflow-x-auto bg-white rounded-xl border border-slate-200 shadow-sm">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr
                    class="border-b border-slate-200 bg-[var(--color-primary)] text-xs font-semibold uppercase tracking-wider text-white">
                    <th class="py-3 px-4">#</th>
                    <th class="py-3 px-4">Question</th>
                    <th class="py-3 px-4">Answer</th>
                    <th class="py-3 px-4">Status</th>
                     @if($this->canUpdate || $this->canDelete || $this->canRead || $this->canCreate)
                        <th class="py-3 px-4 text-center">Action</th>
                    @endif
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 text-sm text-slate-700">
                @forelse($faqs as $index => $faq)
                    <tr class="hover:bg-slate-50 transition">
                        <td class="py-4 px-4 text-slate-500">
                            {{ $faqs->firstItem() + $index }}
                        </td>

                        <td class="py-4 px-4 font-semibold text-slate-900">
                            {{ $faq->question }}
                        </td>

                        <td class="py-4 px-4 text-slate-500 max-w-md truncate">
                            {{ $faq->answer }}
                        </td>

                        <td class="py-4 px-4">
                            @if ($faq->status)
                                <span
                                    class="px-3 py-1 rounded-md bg-[var(--color-primary-tint)] text-[var(--color-primary)] text-xs font-semibold">
                                    Active
                                </span>
                            @else
                                <span class="px-3 py-1 rounded-md bg-red-100 text-red-700 text-xs font-semibold">
                                    Inactive
                                </span>
                            @endif
                        </td>

                         @if($this->canUpdate || $this->canDelete || $this->canRead || $this->canCreate)
                            <td class="py-4 px-4 text-center">
                                <div class="flex justify-center gap-2">
                                    @if ($this->canUpdate)
                                        <x-action-btn icon="edit" tone="edit"
                                            wire:click="editFaq({{ $faq->FaqID }})"
                                            title="Edit FAQ" />
                                    @endif

                                    @if ($this->canDelete)
                                        <x-action-btn icon="delete" tone="delete"
                                            wire:click="confirmDeleteFaq({{ $faq->FaqID }})"
                                            title="Delete FAQ" />
                                    @endif
                                </div>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-8 text-slate-500">
                            No FAQs available.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- PAGINATION -->
        @if ($faqs->hasPages())
            <div class="px-4 py-4 border-t border-slate-200 bg-slate-50 flex justify-center items-center space-x-4">

                {{-- Previous Page Link --}}
                @if ($faqs->onFirstPage())
                    <span class="px-4 py-2 bg-gray-200 text-gray-600 rounded cursor-not-allowed">
                        Previous
                    </span>
                @else
                    <button wire:click="previousPage" wire:loading.attr="disabled"
                        class="px-4 py-2 text-white rounded transition cursor-pointer bg-[var(--color-accent)] hover:bg-[var(--color-accent-dark)]"
                        onmouseout="this.style.backgroundColor='var(--color-accent)'">
                        Previous
                    </button>
                @endif

                <span class="text-sm text-gray-600">
                    Page {{ $faqs->currentPage() }} of {{ $faqs->lastPage() }}
                </span>

                {{-- Next Page Link --}}
                @if ($faqs->hasMorePages())
                    <button wire:click="nextPage" wire:loading.attr="disabled"
                        class="px-4 py-2 text-white rounded transition cursor-pointer bg-[var(--color-primary)] hover:bg-[var(--color-primary-dark)]"
                        onmouseout="this.style.backgroundColor='var(--color-primary)'">
                        Next
                    </button>
                @else
                    <span class="px-4 py-2 bg-gray-200 text-gray-600 rounded cursor-not-allowed">
                        Next
                    </span>
                @endif

            </div>
        @endif
    </div>

    <!-- MODAL -->
    @if ($isOpen)
        <div wire:key="faq-modal"
            class="fixed inset-0 z-[9999] flex items-center justify-center bg-slate-950/70 p-4 backdrop-blur-sm">
            <div class="w-full max-w-lg rounded-2xl bg-white shadow-2xl overflow-hidden">
                <div class="bg-[var(--color-primary)] px-6 py-4 text-white flex justify-between items-center">
                    <h3 class="text-lg font-bold">
                        {{ $faq_id ? 'Edit FAQ' : 'Add New FAQ' }}
                    </h3>
                    <button wire:click="closeModal"
                        class="h-8 w-8 rounded-full bg-white/20 hover:bg-white/30 cursor-pointer flex items-center justify-center">
                        ✕
                    </button>
                </div>

                <form wire:submit.prevent="saveFaq">
                    <div class="p-6 space-y-4">
                        <div>
                            <label class="block text-xs font-bold uppercase text-slate-500 mb-1">
                                Question
                            </label>
                            <input type="text" wire:model="question"
                                class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-[var(--color-primary)]">
                            @error('question')
                                <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase text-slate-500 mb-1">
                                Answer
                            </label>
                            <textarea rows="4" wire:model="answer"
                                class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-[var(--color-primary)]"></textarea>
                            @error('answer')
                                <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase text-slate-500 mb-1">
                                Status
                            </label>
                            <select wire:model="status"
                                class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg bg-white">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 px-6 py-3 border-t bg-slate-50">
                        <button type="button" wire:click="closeModal"
                            class="px-4 py-2 text-xs font-semibold bg-slate-200 rounded-lg hover:bg-slate-300 cursor-pointer">
                            Cancel
                        </button>
                        <button type="submit"
                            class="px-4 py-2 text-xs font-semibold text-white bg-[var(--color-primary)] rounded-lg hover:bg-[var(--color-primary-dark)] cursor-pointer shadow">
                            Save FAQ
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    @script
        <script>
            $wire.on('confirm-delete-faq', (event) => {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: 'var(--color-primary)',
                    cancelButtonColor: 'var(--color-accent)',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $wire.deleteFaq(event.id);
                    }
                });
            });

            $wire.on('faq-deleted', () => {
                Swal.fire({
                    title: 'Deleted!',
                    text: 'FAQ has been successfully deleted.',
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: false
                });
            });
        </script>
    @endscript

</div>
