<div class="members-container w-full px-4 sm:px-6 lg:px-8 py-6">

   <!-- HEADER -->
    <div class="members-header flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="masterlist-title text-2xl font-bold text-slate-800">Members</h2>
            <p class="masterlist-description text-sm text-slate-500 mt-0.5">Member management content and directory.</p>
        </div>

        @if ($this->canCreate)
            <button
                type="button"
                onclick="Livewire.dispatch('open-application-modal')"
                class="masterlist-btn inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold transition cursor-pointer shadow bg-[#0C2C6B] text-white hover:bg-[#09204f] w-full sm:w-auto"
            >
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Add Member
            </button>
        @endif
    </div>

    <!-- SEARCH INPUT, FILTERS & REFRESH BUTTON CONTAINER -->
    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
        <!-- SEARCH INPUT -->
        <div class="relative max-w-sm w-full sm:w-auto flex-1">
            <span class="absolute inset-y-0 left-3 flex items-center text-slate-400 pointer-events-none">
                🔍
            </span>
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Search by fullname or username..."
                class="w-full pl-10 pr-4 py-2 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0C2C6B] bg-white shadow-sm"
            >
        </div>

        <!-- FILTERS AND REFRESH GROUP -->
        <div class="flex items-center gap-3 w-full sm:w-auto justify-end">

            <!-- YEAR FILTER -->
            <div class="relative">
                <select
                    wire:model.live="selectedYear"
                    class="px-3 py-2 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0C2C6B] bg-white text-slate-700 shadow-sm cursor-pointer"
                >
                    @php
                        $currentYear = now()->year;
                    @endphp
                    @for ($year = $currentYear; $year >= 2020; $year--)
                        <option value="{{ $year }}">{{ $year }}</option>
                    @endfor
                </select>
            </div>

            <!-- MONTH FILTER -->
            <div class="relative">
                <select
                    wire:model.live="selectedMonth"
                    class="px-3 py-2 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0C2C6B] bg-white text-slate-700 shadow-sm cursor-pointer"
                >
                    <option value="">All Months</option>
                    <option value="1">January</option>
                    <option value="2">February</option>
                    <option value="3">March</option>
                    <option value="4">April</option>
                    <option value="5">May</option>
                    <option value="6">June</option>
                    <option value="7">July</option>
                    <option value="8">August</option>
                    <option value="9">September</option>
                    <option value="10">October</option>
                    <option value="11">November</option>
                    <option value="12">December</option>
                </select>
            </div>

            <!-- REFRESH BUTTON -->
            <button
                wire:click="resetTable"
                wire:loading.attr="disabled"
                title="Refresh Table"
                class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 text-sm font-semibold rounded-xl shadow-sm transition cursor-pointer"
            >
                <span wire:loading.remove wire:target="resetTable">🔄</span>
                <span wire:loading wire:target="resetTable" class="animate-spin">⏳</span>
                <span class="hidden sm:inline">Refresh</span>
            </button>
        </div>
    </div>

    <!-- MEMBERS TABLE -->
    <div class="member-table overflow-x-auto bg-white rounded-xl border border-slate-200 shadow-sm">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr
                    class="border-b border-slate-200 bg-[#0C2C6B] text-xs font-semibold uppercase tracking-wider text-white">
                    <th class="py-3 px-4">#</th>
                    <th class="py-3 px-4">Name</th>
                    <th class="py-3 px-4">Contact</th>
                    <th class="py-3 px-4">Gender</th>
                    <th class="py-3 px-4">Birthday</th>
                    <th class="py-3 px-4">Status</th>
                     @if($this->canUpdate || $this->canDelete || $this->canRead || $this->canCreate)
                    <th class="py-3 px-4 text-center">Action</th>
                    @endif
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 text-sm text-slate-700">
                @forelse($members as $index => $member)
                    <tr wire:key="member-row-{{ $member->UserID }}" class="hover:bg-slate-50 transition-colors">
                        <td class="py-4 px-4 font-medium text-slate-500">
                            {{ $members->firstItem() + $index }}
                        </td>

                        <td class="py-4 px-4 font-semibold text-slate-900">
                            {{ $member->firstname }}
                            {{ $member->middlename }}
                            {{ $member->lastname }}
                        </td>

                        <td class="py-4 px-4 text-slate-500">
                            {{ $member->contactno }}
                        </td>

                        <td class="py-4 px-4">
                            <span
                                class="inline-flex justify-center items-center px-3 py-1 rounded-md bg-slate-100 text-slate-700 text-xs font-semibold">
                                {{ $member->gender }}
                            </span>
                        </td>

                        <td class="py-4 px-4 text-slate-500">
                            {{ $member->birthdate ? \Carbon\Carbon::parse($member->birthdate)->format('F d, Y') : '-' }}
                        </td>

                        <td class="py-4 px-4">
                            @if (strtolower($member->status) === 'active')
                                <span
                                    class="inline-flex justify-center items-center w-24 px-3 py-1 rounded-md bg-[#0C2C6B] text-white text-xs font-semibold shadow-sm">
                                    Active
                                </span>
                            @else
                                <span
                                    class="inline-flex justify-center items-center w-24 px-3 py-1 rounded-md bg-slate-600 text-white text-xs font-semibold shadow-sm">
                                    Inactive
                                </span>
                            @endif
                        </td>

                        <!-- ACTIONS COLUMN -->
                         @if($this->canUpdate || $this->canDelete || $this->canRead || $this->canCreate)
                        <td class="py-4 px-4 text-center">

                            <div class="flex gap-2 justify-center items-center">

                                <!-- VIEW -->
                                @if ($this->canRead)
                                    <button wire:click="viewMember({{ $member->UserID }})" title="View Member"
                                        class="inline-flex items-center justify-center px-3 py-2 rounded-lg bg-slate-600 text-white hover:bg-slate-700 transition cursor-pointer text-xs font-semibold shadow">
                                        👁️
                                    </button>
                                @endif

                                <!-- EDIT / UPDATE -->
                                @if ($this->canUpdate)
                                    <button wire:click="editMember({{ $member->UserID }})" title="Update Member"
                                        class="inline-flex items-center justify-center px-3 py-2 rounded-lg bg-[#0C2C6B] text-white hover:bg-[#09204f] transition cursor-pointer text-xs font-semibold shadow">
                                        ✏️
                                    </button>
                                @endif

                                <!-- STATUS / ACTIVATION ACTIONS (Requires Update Permission) -->
                                @if ($this->canUpdate)
                                    @if (strtolower($member->status) === 'active')
                                        <button wire:click="confirmStatus({{ $member->UserID }})"
                                            title="Deactivate Member"
                                            class="inline-flex items-center justify-center w-24 px-3 py-2 rounded-lg bg-red-500 text-white hover:bg-red-600 transition cursor-pointer text-xs font-semibold shadow">
                                            Deactivate
                                        </button>
                                    @elseif($member->has_pending_activation)
                                        <button wire:click="openReview({{ $member->UserID }})"
                                            title="Review Activation"
                                            class="inline-flex items-center justify-center w-24 px-3 py-2 rounded-lg bg-amber-500 text-white hover:bg-amber-600 transition cursor-pointer text-xs font-semibold shadow">
                                            Pending
                                        </button>
                                    @else
                                        <button wire:click="openActivation({{ $member->UserID }})"
                                            title="Activate Member"
                                            class="inline-flex items-center justify-center w-24 px-3 py-2 rounded-lg bg-[#0C2C6B] text-white hover:bg-[#09204f] transition cursor-pointer text-xs font-semibold shadow">
                                            Activate
                                        </button>
                                    @endif
                                @endif

                            </div>
                        </td>
                        @endif

                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-8 text-slate-500">
                            No members found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- PAGINATION -->
        @if ($members->hasPages())
            <div class="px-4 py-4 border-t border-slate-200 bg-slate-50 flex justify-center items-center space-x-4">

                {{-- Previous Page Link --}}
                @if ($members->onFirstPage())
                    <span class="px-4 py-2 bg-gray-200 text-gray-400 rounded cursor-not-allowed">
                        Previous
                    </span>
                @else
                    <button wire:click="previousPage" wire:loading.attr="disabled"
                        class="px-4 py-2 text-white rounded transition cursor-pointer"
                        style="background-color: #D79B10;" onmouseover="this.style.backgroundColor='#c58d0f'"
                        onmouseout="this.style.backgroundColor='#D79B10'">
                        Previous
                    </button>
                @endif

                <span class="text-sm text-gray-600">
                    Page {{ $members->currentPage() }} of {{ $members->lastPage() }}
                </span>

                {{-- Next Page Link --}}
                @if ($members->hasMorePages())
                    <button wire:click="nextPage" wire:loading.attr="disabled"
                        class="px-4 py-2 text-white rounded transition cursor-pointer"
                        style="background-color: #0C2C6B;" onmouseover="this.style.backgroundColor='#0a2559'"
                        onmouseout="this.style.backgroundColor='#0C2C6B'">
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

    <!-- LIVEWIRE MODAL COMPONENT -->
    <livewire:member-pages.my-profile.activation-modal />


    <!-- SWEETALERT SCRIPTS -->
    @script
        <script>
            $wire.on('confirm-status', (data) => {
                const actionText = data.action === 'deactivate' ?
                    "I'm sure to deactivate" :
                    "I'm sure to activate";

                Swal.fire({
                    title: 'Are you sure?',
                    text: 'Type the confirmation text below to continue.',
                    icon: 'warning',
                    input: 'text',
                    inputPlaceholder: actionText,
                    inputAttributes: {
                        autocapitalize: 'off'
                    },
                    showCancelButton: true,
                    confirmButtonColor: '#0C2C6B',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Confirm ' + data.action,
                    cancelButtonText: 'Cancel',
                    preConfirm: (value) => {
                        if (value !== actionText) {
                            Swal.showValidationMessage('Please type: ' + actionText);
                            return false;
                        }
                        return true;
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Updating...',
                            text: 'Please wait.',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            showConfirmButton: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        $wire.updateStatus(data.id);
                    }
                });
            });

            $wire.on('status-updated', () => {
                Swal.fire({
                    title: 'Updated!',
                    text: 'Member status has been updated.',
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: false
                });
            });
        </script>
    @endscript

</div>
