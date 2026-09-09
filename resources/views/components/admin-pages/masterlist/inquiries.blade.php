<div class="inquiries-container w-full px-4 sm:px-6 lg:px-8 py-6">

    <!-- HEADER -->
    <div class="members-header mb-2">
        <h2 class="masterlist-title text-2xl font-bold text-slate-800">Inquiries</h2>
    </div>

    <p class="masterlist-description text-sm text-slate-500 mb-6">
        Manage and review member inquiries.
    </p>

    <!-- SEARCH INPUT, DATE FILTER & REFRESH BUTTON CONTAINER -->
    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
        <!-- SEARCH INPUT -->
        <div class="relative max-w-sm w-full sm:w-auto flex-1">
            <span class="absolute inset-y-0 left-3 flex items-center text-slate-400 pointer-events-none">
                🔍
            </span>
            <input type="text" wire:model.live="search" placeholder="Search inquiries..."
                class="w-full pl-10 pr-4 py-2 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0C2C6B] bg-white shadow-sm">
        </div>

        <!-- FILTERS AND REFRESH GROUP -->
        <div class="flex items-center gap-3 w-full sm:w-auto justify-end">

            <!-- DATE RANGE FILTER -->
            <div wire:ignore class="relative w-72">
                <span class="absolute inset-y-0 left-3 flex items-center text-slate-400 pointer-events-none z-10">
                    📅
                </span>
                <input type="text" id="inquiryDateRange" readonly
                    class="date-range-input pl-9 pr-3 py-2 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0C2C6B] bg-white text-slate-700 shadow-sm cursor-pointer w-full text-center font-medium"
                    placeholder="Select date range">
            </div>

            @if ($dateRange)
                <button type="button" wire:click="clearDateFilter" title="Clear date filter"
                    class="inline-flex items-center px-3 py-2 text-sm text-slate-500 hover:text-red-500 hover:border-red-200 hover:bg-red-50 rounded-xl border border-slate-200 bg-white shadow-sm cursor-pointer transition">
                    ✕
                </button>
            @endif

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
    </div>

    <!-- TABLE -->
    <div class="member-table w-full overflow-x-auto rounded-xl border border-slate-200 bg-white shadow-sm">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr
                    class="border-b border-slate-200 bg-slate-50 text-xs font-semibold uppercase tracking-wider text-slate-600">
                    <th class="py-3 px-4">#</th>
                    <th class="py-3 px-4">Name</th>
                    <th class="py-3 px-4">Subject</th>
                    <th class="py-3 px-4">Category</th>
                    <th class="py-3 px-4">Date Read</th>
                    <th class="py-3 px-4">Status</th>
                    @if ($this->canUpdate || $this->canDelete || $this->canRead || $this->canCreate)
                        <th class="py-3 px-4 text-center">Action</th>
                    @endif
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 text-sm text-slate-700">
                @forelse ($inquiries as $index => $inquiry)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="py-4 px-4 font-medium text-slate-500">
                            {{ $index + 1 }}
                        </td>
                        <td class="py-4 px-4">
                            <div class="font-semibold text-slate-900">
                                {{ $inquiry->name }}
                            </div>
                            <div class="text-xs text-slate-500">
                                {{ $inquiry->email }}
                            </div>
                        </td>
                        <td class="py-4 px-4 font-medium text-slate-800">
                            {{ $inquiry->subject }}
                        </td>
                        <td class="py-4 px-4">
                            <span
                                class="inline-flex justify-center items-center px-3 py-1 rounded-md bg-slate-100 text-slate-700 text-xs font-semibold">
                                {{ $inquiry->category ?: 'General' }}
                            </span>
                        </td>
                        <td class="py-4 px-4 text-xs text-slate-500">
                            {{ $inquiry->date_read ? \Carbon\Carbon::parse($inquiry->date_read)->format('M d, Y h:i A') : 'Not yet read' }}
                        </td>
                        <td class="py-4 px-4">
                            @if (is_null($inquiry->date_read))
                                <span
                                    class="inline-flex justify-center items-center px-3 py-1 rounded-md bg-[#D79B10] text-white text-xs font-semibold">
                                    Unread
                                </span>
                            @else
                                <span
                                    class="inline-flex justify-center items-center px-3 py-1 rounded-md bg-[#0C2C6B] text-white text-xs font-semibold">
                                    Read
                                </span>
                            @endif
                        </td>
                        @if ($this->canCreate)
                            <td class="py-4 px-4 text-center">
                                <div class="flex justify-center">
                                    <button wire:click="viewInquiry({{ $inquiry->ID }})"
                                        class="inline-flex items-center justify-center px-4 py-2 rounded-lg bg-[#0C2C6B] text-white hover:bg-[#09204f] text-xs font-semibold shadow transition cursor-pointer">
                                        View
                                    </button>
                                </div>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-8 text-slate-500">
                            No inquiries found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- VIEW INQUIRY MODAL -->
    @if ($showModal && $selectedInquiry)
        <div
            class="fixed inset-0 z-[9999] flex items-center justify-center bg-slate-950/70 p-4 backdrop-blur-sm overflow-y-auto">

            <div class="w-full max-w-2xl overflow-hidden rounded-2xl bg-white shadow-2xl my-8">

                <!-- MODAL HEADER -->
                <div class="bg-[#0C2C6B] px-6 py-4 text-white flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-bold">Inquiry Details & Reply</h3>
                        <p class="text-xs text-blue-100">Review member inquiry and send a response.</p>
                    </div>
                    <button wire:click="closeModal"
                        class="flex h-8 w-8 items-center justify-center rounded-full bg-white/20 transition hover:bg-white/30 text-white font-bold cursor-pointer">
                        ✕
                    </button>
                </div>

                <!-- MODAL BODY -->
                <div class="p-6 space-y-4 max-h-[75vh] overflow-y-auto">

                    <!-- SUCCESS MESSAGE -->
                    @if ($successMessage)
                        <div
                            class="rounded-xl bg-emerald-50 border border-emerald-200 p-3 text-xs font-semibold text-emerald-700">
                            {{ $successMessage }}
                        </div>
                    @endif

                    <!-- INFO GRID -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="rounded-xl border border-slate-200 p-3 bg-slate-50/50">
                            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Full Name</p>
                            <p class="mt-1 text-sm font-semibold text-slate-800">{{ $selectedInquiry->name }}</p>
                        </div>

                        <div class="rounded-xl border border-slate-200 p-3 bg-slate-50/50">
                            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Email Address</p>
                            <p class="mt-1 text-sm font-semibold text-slate-800 break-all">{{ $selectedInquiry->email }}
                            </p>
                        </div>

                        <div class="rounded-xl border border-slate-200 p-3 bg-slate-50/50">
                            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Subject</p>
                            <p class="mt-1 text-sm font-semibold text-slate-800">{{ $selectedInquiry->subject }}</p>
                        </div>

                        <div class="rounded-xl border border-slate-200 p-3 bg-slate-50/50">
                            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Category</p>
                            <div class="mt-1">
                                <span
                                    class="inline-flex rounded-md bg-[#0C2C6B] px-2.5 py-0.5 text-xs font-semibold text-white">
                                    {{ $selectedInquiry->category ?: 'General' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- STATUS & DATE BLOCK -->
                    <div
                        class="flex flex-col sm:flex-row items-start sm:items-center justify-between rounded-xl bg-slate-100 p-4 gap-2">
                        <div>
                            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Status</p>
                            <div class="mt-1">
                                @if ($selectedInquiry->date_read)
                                    <span
                                        class="inline-flex rounded-md bg-[#0C2C6B] px-2.5 py-0.5 text-xs font-semibold text-white">
                                        Read
                                    </span>
                                @else
                                    <span
                                        class="inline-flex rounded-md bg-[#D79B10] px-2.5 py-0.5 text-xs font-semibold text-white">
                                        Unread
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="sm:text-right">
                            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Date Read</p>
                            <p class="mt-1 text-xs font-semibold text-slate-700">
                                {{ $selectedInquiry->date_read ? \Carbon\Carbon::parse($selectedInquiry->date_read)->format('F d, Y h:i A') : 'Not yet viewed' }}
                            </p>
                        </div>
                    </div>

                    <!-- MESSAGE BLOCK -->
                    <div>
                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">Original Message</h4>
                        <div
                            class="rounded-xl border border-slate-200 bg-slate-50 p-4 text-xs sm:text-sm leading-relaxed text-slate-700 whitespace-pre-line max-h-36 overflow-y-auto">
                            {{ $selectedInquiry->details }}
                        </div>
                    </div>

                    @if ($this->canCreate)
                        <!-- REPLY FORM SECTION -->
                        <div class="border-t border-slate-200 pt-4 mt-4 space-y-3">
                            <h4 class="text-xs font-bold uppercase tracking-wider text-[#0C2C6B]">Send Email Reply</h4>

                            <div>
                                <label
                                    class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">Reply
                                    Subject</label>
                                <input type="text" readonly wire:model="replySubject"
                                    class="w-full rounded-xl border border-slate-200 bg-slate-50 p-2.5 text-xs sm:text-sm text-slate-800 focus:outline-none cursor-not-allowed">
                                @error('replySubject')
                                    <span class="text-xs text-red-500">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label
                                    class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1">Reply
                                    Message*</label>
                                <textarea wire:model="replyMessage" rows="4" placeholder="Type your response here..."
                                    class="w-full rounded-xl border border-slate-200 p-3 text-xs sm:text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#0C2C6B]"></textarea>
                                @error('replyMessage')
                                    <span class="text-xs text-red-500">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="flex justify-end">
                                <button wire:click="sendReply"
                                    class="rounded-lg bg-[#D79B10] px-4 py-2 text-xs font-semibold text-white transition hover:bg-[#b8820c] cursor-pointer">
                                    Send Reply
                                </button>
                            </div>
                        </div>
                    @endif

                </div>

                <!-- MODAL FOOTER -->
                <div class="flex justify-end border-t border-slate-200 px-6 py-3 bg-slate-50">
                    <button wire:click="closeModal"
                        class="rounded-lg bg-[#0C2C6B] px-4 py-2 text-xs font-semibold text-white transition hover:bg-[#09204f] cursor-pointer">
                        Close
                    </button>
                </div>

            </div>

        </div>
    @endif

    @script
<script>
    const dateInput = $wire.$el.querySelector('#inquiryDateRange');

    const picker = flatpickr(dateInput, {
        mode: 'range',
        dateFormat: 'Y-m-d',
        altInput: true,
        altFormat: 'M d, Y',
        maxDate: 'today',
        defaultDate: @js($dateRange ? explode(' to ', $dateRange) : []),
        onChange: (selectedDates, dateStr, instance) => {
            if (selectedDates.length === 2) {
                const start = instance.formatDate(selectedDates[0], 'Y-m-d');
                const end = instance.formatDate(selectedDates[1], 'Y-m-d');
                $wire.set('dateRange', `${start} to ${end}`);
            } else if (selectedDates.length === 0) {
                $wire.set('dateRange', '');
            }
        },
        onClose: (selectedDates, dateStr, instance) => {
            if (selectedDates.length === 1) {
                const day = instance.formatDate(selectedDates[0], 'Y-m-d');
                instance.setDate([selectedDates[0], selectedDates[0]], false);
                $wire.set('dateRange', `${day} to ${day}`);
            }
        }
    });

    $wire.on('ranges-reset', () => {
        picker.clear();
    });
</script>
@endscript

</div>
