
<div class="benefits-container w-full px-4 sm:px-6 lg:px-8 py-6">

    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">
                Branch Variant Grants
            </h2>
            <p class="text-sm text-slate-500 mt-1">
                Control which membership variants each branch can use.
            </p>
        </div>

        @if ($this->canCreate)
            <button
                type="button"
                wire:click="openCreateModal"
                class="px-4 py-2.5 rounded-xl text-sm font-semibold bg-[var(--color-primary)] text-white hover:bg-[var(--color-primary-dark)] shadow"
            >
                + Grant Variants
            </button>
        @endif
    </div>

    {{-- FLASH MESSAGES --}}
    @foreach (['success', 'error'] as $messageType)
        @if (session()->has($messageType))
            <div @class([
                'p-4 mb-4 rounded-xl border text-sm font-medium',
                'bg-[var(--color-primary-tint)] text-[var(--color-primary)] border-[var(--color-primary-tint-border)]'
                    => $messageType === 'success',
                'bg-red-50 text-red-700 border-red-200'
                    => $messageType === 'error',
            ])>
                {{ session($messageType) }}
            </div>
        @endif
    @endforeach

    @if ($errors->any())
        <div class="p-4 mb-4 rounded-xl border border-red-200 bg-red-50 text-red-700 text-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- SEARCH --}}
    <div class="flex flex-col sm:flex-row sm:items-center gap-3 mb-6">
        <input
            type="text"
            wire:model.live.debounce.300ms="search"
            placeholder="Search branch, variant, or status..."
            class="w-full sm:flex-1 px-4 py-2.5 text-sm border border-slate-200 rounded-xl focus:ring-2 focus:ring-[var(--color-primary)] focus:outline-none"
        >

        <button
            type="button"
            wire:click="resetTable"
            wire:loading.attr="disabled"
            wire:target="resetTable"
            class="px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold hover:bg-slate-50"
        >
            Refresh
        </button>

        <span class="text-sm text-slate-500 whitespace-nowrap">
            {{ $grants->total() }} grant(s)
        </span>
    </div>

    {{-- GRANTS TABLE --}}
    <div class="overflow-x-auto bg-white rounded-xl border border-slate-200 shadow-sm">
        <table class="w-full min-w-[850px] text-left border-collapse">

            <thead>
                <tr class="bg-[var(--color-primary)] text-white text-xs uppercase tracking-wider">
                    <th class="px-4 py-3">#</th>
                    <th class="px-4 py-3">Branch</th>
                    <th class="px-4 py-3">Variant</th>
                    <th class="px-4 py-3">Grant Status</th>
                    <th class="px-4 py-3">Created At</th>
                    <th class="px-4 py-3">Updated At</th>

                    @if ($this->canUpdate || $this->canDelete)
                        <th class="px-4 py-3 text-center">Actions</th>
                    @endif
                </tr>
            </thead>

            <tbody class="divide-y divide-slate-200 text-sm text-slate-700">
                @forelse ($grants as $index => $grant)
                    <tr wire:key="grant-{{ $grant->GrantID }}"
                        class="hover:bg-slate-50">

                        <td class="px-4 py-4 text-slate-500">
                            {{ $grants->firstItem() + $index }}
                        </td>

                        <td class="px-4 py-4 font-semibold text-slate-900">
                            {{ $grant->branch?->branchname ?? 'Branch unavailable' }}
                        </td>

                        <td class="px-4 py-4">
                            <div class="font-semibold text-slate-900">
                                {{ $grant->variant?->name ?? 'Variant unavailable' }}
                            </div>
                            <div class="text-xs text-slate-500 mt-1">
                                VID: {{ $grant->VID }}
                            </div>
                        </td>

                        <td class="px-4 py-4">
                            <span @class([
                                'inline-flex px-3 py-1 rounded-md text-xs font-semibold',
                                'bg-[var(--color-primary-tint)] text-[var(--color-primary)]'
                                    => $grant->status === 'Active',
                                'bg-slate-100 text-slate-600'
                                    => $grant->status !== 'Active',
                            ])>
                                {{ $grant->status }}
                            </span>
                        </td>

                        <td class="px-4 py-4 text-slate-500 whitespace-nowrap">
                            {{ $grant->created_at?->format('M d, Y h:i A') ?? '—' }}
                        </td>

                        <td class="px-4 py-4 text-slate-500 whitespace-nowrap">
                            {{ $grant->updated_at?->format('M d, Y h:i A') ?? '—' }}
                        </td>

                        @if ($this->canUpdate || $this->canDelete)
                            <td class="px-4 py-4">
                                <div class="flex justify-center gap-2">

                                    @if ($this->canUpdate)
                                        <x-action-btn icon="edit" tone="edit"
                                            wire:click="editGrant({{ $grant->GrantID }})"
                                            title="Edit grant" />

                                        <x-action-btn
                                            :icon="$grant->status === 'Active' ? 'reject' : 'approve'"
                                            :tone="$grant->status === 'Active' ? 'delete' : 'approve'"
                                            wire:click="toggleStatus({{ $grant->GrantID }})"
                                            wire:confirm="Change the status of this grant?"
                                            title="{{ $grant->status === 'Active' ? 'Disable grant' : 'Enable grant' }}" />
                                    @endif

                                    @if ($this->canDelete)
                                        <x-action-btn icon="delete" tone="delete"
                                            wire:click="deleteGrant({{ $grant->GrantID }})"
                                            wire:confirm="Delete this branch variant grant?"
                                            title="Delete grant" />
                                    @endif

                                </div>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ ($this->canUpdate || $this->canDelete) ? 7 : 6 }}"
                            class="px-4 py-12 text-center text-slate-500">
                            <p class="font-semibold text-slate-700">
                                No branch variant grants found.
                            </p>
                            <p class="text-sm mt-1">
                                Grant variants to a branch to get started.
                            </p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- PAGINATION --}}
        @if ($grants->hasPages())
            <div class="px-4 py-4 border-t border-slate-200 bg-slate-50 flex flex-wrap justify-center items-center gap-4">

                <button
                    type="button"
                    wire:click="previousPage"
                    @disabled($grants->onFirstPage())
                    class="px-4 py-2 rounded-lg bg-[var(--color-accent)] text-white disabled:opacity-40"
                >
                    Previous
                </button>

                <span class="text-sm text-slate-600">
                    Page {{ $grants->currentPage() }} of {{ $grants->lastPage() }}
                </span>

                <button
                    type="button"
                    wire:click="nextPage"
                    @disabled(!$grants->hasMorePages())
                    class="px-4 py-2 rounded-lg bg-[var(--color-primary)] text-white disabled:opacity-40"
                >
                    Next
                </button>
            </div>
        @endif
    </div>

    {{-- CREATE / EDIT GRANT MODAL --}}
    @if ($isOpen)
        <div class="fixed inset-0 z-[9999] flex items-center justify-center bg-slate-950/70 p-4 backdrop-blur-sm overflow-y-auto">

            <div class="w-full max-w-lg rounded-2xl bg-white shadow-2xl overflow-hidden my-8">

                <div class="bg-[var(--color-primary)] px-6 py-4 text-white flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-bold">
                            {{ $grant_id ? 'Edit Variant Grant' : 'Grant Variants to Branch' }}
                        </h3>
                        <p class="text-xs text-[var(--color-primary-tint)] mt-1">
                            Select a branch and choose its permitted variants.
                        </p>
                    </div>

                    <button
                        type="button"
                        wire:click="closeModal"
                        class="h-8 w-8 rounded-full bg-white/20 hover:bg-white/30"
                    >
                        ✕
                    </button>
                </div>

                <form wire:submit.prevent="saveGrant">
                    <div class="p-6 space-y-5 max-h-[70vh] overflow-y-auto">

                        {{-- BRANCH --}}
                        <div>
                            <label for="grant-branch"
                                class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">
                                Branch <span class="text-red-500">*</span>
                            </label>

                            <select
                                id="grant-branch"
                                wire:model="branch_id"
                                class="w-full px-3 py-2.5 border border-slate-200 rounded-lg bg-white focus:ring-2 focus:ring-[var(--color-primary)] focus:outline-none"
                            >
                                <option value="">Select a branch</option>

                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->BranchID }}">
                                        {{ $branch->branchname }}
                                    </option>
                                @endforeach
                            </select>

                            @error('branch_id')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- GRANT ALL --}}
                        @if (!$grant_id)
                            <label class="flex items-start gap-3 p-4 rounded-xl border border-[var(--color-primary-tint-border)] bg-[var(--color-primary-tint)] cursor-pointer">
                                <input
                                    type="checkbox"
                                    wire:model.live="grantAllVariants"
                                    class="mt-1 rounded text-[var(--color-primary)] focus:ring-[var(--color-primary)]"
                                >

                                <span>
                                    <span class="block font-semibold text-slate-800">
                                        Grant all active variants
                                    </span>
                                    <span class="block text-xs text-slate-500 mt-1">
                                        Creates a separate grant record for each active variant.
                                    </span>
                                </span>
                            </label>
                        @endif

                        {{-- INDIVIDUAL VARIANTS --}}
                        @if (!$grantAllVariants)
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">
                                    Select Variant(s) <span class="text-red-500">*</span>
                                </label>

                                @if ($variants->isEmpty())
                                    <p class="text-sm text-amber-600">
                                        No active variants are available.
                                    </p>
                                @else
                                    <div class="border border-slate-200 rounded-xl divide-y divide-slate-100 max-h-56 overflow-y-auto">
                                        @foreach ($variants as $variant)
                                            <label
                                                wire:key="select-variant-{{ $variant->VID }}"
                                                class="flex items-start gap-3 p-3 hover:bg-slate-50 cursor-pointer"
                                            >
                                                <input
                                                    type="checkbox"
                                                    wire:model="selectedVariantIds"
                                                    value="{{ $variant->VID }}"
                                                    class="mt-1 rounded text-[var(--color-primary)] focus:ring-[var(--color-primary)]"
                                                >

                                                <span>
                                                    <span class="block font-semibold text-slate-800">
                                                        {{ $variant->name }}
                                                    </span>
                                                    <span class="block text-xs text-slate-500">
                                                        {{ $variant->description ?: 'No description' }}
                                                    </span>
                                                </span>
                                            </label>
                                        @endforeach
                                    </div>
                                @endif

                                @error('selectedVariantIds')
                                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                @enderror

                                @error('selectedVariantIds.*')
                                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        @endif

                        {{-- STATUS --}}
                        <div>
                            <label for="grant-status"
                                class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">
                                Grant Status
                            </label>

                            <select
                                id="grant-status"
                                wire:model="status"
                                class="w-full px-3 py-2.5 border border-slate-200 rounded-lg bg-white focus:ring-2 focus:ring-[var(--color-primary)] focus:outline-none"
                            >
                                <option value="Active">Active</option>
                                <option value="Inactive">Inactive</option>
                            </select>

                            @error('status')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 border-t border-slate-200 px-6 py-4 bg-slate-50">
                        <button
                            type="button"
                            wire:click="closeModal"
                            class="px-4 py-2 text-sm font-semibold text-slate-700 bg-slate-200 rounded-lg hover:bg-slate-300"
                        >
                            Cancel
                        </button>

                        <button
                            type="submit"
                            wire:loading.attr="disabled"
                            wire:target="saveGrant"
                            class="px-4 py-2 text-sm font-semibold text-white bg-[var(--color-primary)] rounded-lg hover:bg-[var(--color-primary-dark)] disabled:opacity-50"
                        >
                            <span wire:loading.remove wire:target="saveGrant">
                                {{ $grant_id ? 'Save Changes' : 'Save Grants' }}
                            </span>

                            <span wire:loading wire:target="saveGrant">
                                Saving...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
