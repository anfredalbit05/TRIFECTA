{{--
=====================================================================
 FREE + REGULAR MEMBERS MODAL
=====================================================================
 Backing component:
 App\Livewire\AdminPages\Masterlist\SatelliteMembersComponents\FreeMembersModal
 Opened via the 'open-free-members' event (dispatched with UserID).

 Lists every member under one satellite member — matched through the
 satellite member's referralCode used as the member's uplineCode:

   • FREE members (activations.isFree = true)  → one-click "Activate"
     with a typed confirmation (preview + type-the-username).
   • REGULAR members (isFree = false)          → "Approve" button that
     opens the shared ActivationReviewModal — the exact same review
     process used for normal members.

 "Activate All" only ever touches Pending FREE rows (typed confirmation
 required as well); regular members are never batch-approved here.

 Approval of a free member is one-click (no policy / receipt numbers):
 the shared ActivationApprovalService completes the member's Pending
 membership-fee ledger and pays the satellite owner DR / TX / OV / PF.

 Layout & color palette: PRIMARY (var(--color-primary)) + ACCENT (var(--color-accent)) ONLY —
 no violet / green / rose anywhere in this modal.
=====================================================================
--}}

<div>
    @if ($open)

        {{-- ============================= BACKDROP ============================= --}}
        <div class="fixed inset-0 z-[9999] flex items-center justify-center overflow-y-auto bg-black/70 p-3 backdrop-blur-sm sm:p-4">

            {{-- ============================= MODAL BOX ============================= --}}
            <div class="my-auto flex max-h-[90vh] w-full max-w-4xl flex-col overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-2xl">

                {{-- ======================== HEADER ======================== --}}
                <div class="sticky top-0 z-10 flex items-center justify-between border-b border-slate-200 bg-white px-5 py-4 shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-[var(--color-primary)] text-lg font-extrabold text-white">
                            F
                        </div>
                        <div>
                            <h2 class="text-lg font-extrabold text-[var(--color-primary)] sm:text-xl">
                                Free Members and Regular Members
                            </h2>
                            <div class="mt-0.5 flex flex-wrap items-center gap-2 text-xs text-slate-500">
                                <span class="font-semibold text-slate-700">
                                    {{ $satellite?->satellite_name }}
                                </span>

                                @if ($satellite)
                                    <span class="inline-flex rounded-full bg-[var(--color-accent)]/15 px-2.5 py-0.5 text-[10px] font-bold uppercase text-[var(--color-accent-darker)]">
                                        {{ config('satellite.tier_by_type')[(int) $satellite->satellite_typeID] ?? 'Satellite' }}
                                    </span>
                                @endif

                                <span class="inline-flex items-center gap-1.5 rounded-full bg-[var(--color-accent)]/20 px-2.5 py-0.5 text-[10px] font-bold uppercase text-[var(--color-accent-darker)]">
                                    {{ $pendingCount }} pending
                                </span>

                                <span class="inline-flex items-center gap-1.5 rounded-full bg-[var(--color-primary-tint)] px-2.5 py-0.5 text-[10px] font-bold uppercase text-[var(--color-primary)]">
                                    {{ $approvedCount }} active
                                </span>
                            </div>
                        </div>
                    </div>

                    <button
                        wire:click="close"
                        type="button"
                        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-slate-100 font-bold text-slate-500 transition hover:bg-[var(--color-primary-tint)] hover:text-[var(--color-primary)]"
                        title="Close">
                        &#10005;
                    </button>
                </div>

                {{-- ======================== BODY ======================== --}}
                <div class="overflow-y-auto p-4 sm:p-6">

                    {{-- SUMMARY — blue + yellow only --}}
                    <div class="mb-4 grid grid-cols-3 gap-3">
                        <div class="rounded-xl border border-[var(--color-primary-tint-border)] bg-[var(--color-primary-tint)] p-3 text-center">
                            <p class="text-[10px] font-bold uppercase tracking-wider text-[var(--color-primary)]">Total</p>
                            <p class="text-xl font-extrabold text-[var(--color-primary)]">{{ count($freeMembers) }}</p>
                        </div>
                        <div class="rounded-xl border border-[var(--color-accent)]/60 bg-[var(--color-accent)]/10 p-3 text-center">
                            <p class="text-[10px] font-bold uppercase tracking-wider text-[var(--color-accent-darker)]">Pending</p>
                            <p class="text-xl font-extrabold text-[var(--color-accent-darker)]">{{ $pendingCount }}</p>
                        </div>
                        <div class="rounded-xl border border-[var(--color-primary-tint-border)] bg-[var(--color-primary-tint)] p-3 text-center">
                            <p class="text-[10px] font-bold uppercase tracking-wider text-[var(--color-primary)]">Approved</p>
                            <p class="text-xl font-extrabold text-[var(--color-primary)]">{{ $approvedCount }}</p>
                        </div>
                    </div>

                    {{-- MEMBERS TABLE --}}
                    @if (count($freeMembers))

                        <div class="overflow-x-auto rounded-xl border border-slate-200">
                            <table class="min-w-full divide-y divide-slate-200 text-sm">
                                <thead class="bg-[var(--color-primary)]">
                                    <tr class="text-left text-[11px] uppercase tracking-wider text-white">
                                        <th class="px-3 py-3 font-bold">#</th>
                                        <th class="px-3 py-3 font-bold">Username</th>
                                        <th class="px-3 py-3 font-bold">Name</th>
                                        <th class="px-3 py-3 font-bold">Account</th>
                                        <th class="px-3 py-3 font-bold">Member Type</th>
                                        <th class="px-3 py-3 font-bold">Activation</th>
                                        <th class="px-3 py-3 text-right font-bold">Amount</th>
                                        <th class="px-3 py-3 text-center font-bold">Action</th>
                                    </tr>
                                </thead>

                                <tbody class="divide-y divide-slate-100 bg-white">
                                    @foreach ($freeMembers as $index => $row)
                                        <tr class="{{ $row['status'] === 'Pending' ? 'bg-[var(--color-accent)]/10' : '' }} transition-colors hover:bg-[var(--color-primary-tint)]/60">
                                            <td class="px-3 py-2.5 font-semibold text-slate-500">
                                                {{ $index + 1 }}
                                            </td>

                                            <td class="px-3 py-2.5 font-medium text-slate-800">
                                                {{ $row['username'] }}
                                            </td>

                                            <td class="px-3 py-2.5 text-slate-700">
                                                {{ $row['name'] }}
                                            </td>

                                            <td class="px-3 py-2.5">
                                                <span class="inline-flex rounded-full px-2.5 py-0.5 text-[10px] font-bold uppercase {{ $row['user_status'] === 'Active' ? 'bg-[var(--color-primary-tint)] text-[var(--color-primary)]' : 'bg-slate-100 text-slate-600' }}">
                                                    {{ $row['user_status'] ?: 'Inactive' }}
                                                </span>
                                            </td>

                                            <td class="px-3 py-2.5">
                                                @if ($row['isFree'])
                                                    <span class="inline-flex rounded-full bg-[var(--color-primary)] px-2.5 py-0.5 text-[10px] font-bold uppercase text-white">
                                                        FREE
                                                    </span>
                                                @else
                                                    <span class="inline-flex rounded-full bg-[var(--color-accent)] px-2.5 py-0.5 text-[10px] font-bold uppercase text-white">
                                                        REGULAR
                                                    </span>
                                                @endif
                                            </td>

                                            <td class="px-3 py-2.5">
                                                <span class="inline-flex rounded-full px-2.5 py-0.5 text-[10px] font-bold uppercase {{ $row['status'] === 'Approved' ? 'bg-[var(--color-primary-tint)] text-[var(--color-primary)]' : ($row['status'] === 'Pending' ? 'bg-[var(--color-accent)]/25 text-[var(--color-accent-darker)]' : 'bg-slate-200 text-slate-600') }}">
                                                    {{ $row['status'] }}
                                                </span>
                                            </td>

                                            <td class="px-3 py-2.5 text-right font-semibold text-slate-700">
                                                ₱{{ number_format($row['amount'], 2) }}
                                            </td>

                                            <td class="px-3 py-2.5 text-center">
                                                @if ($row['status'] === 'Pending')
                                                    @if ($this->canUpdate)
                                                        @if ($row['isFree'])
                                                            {{-- FREE member → one-click activate (typed confirmation) --}}
                                                            <button
                                                                type="button"
                                                                wire:click="confirmApproveFreeMember({{ $row['ActivationID'] }})"
                                                                wire:loading.attr="disabled"
                                                                title="Activate Free Member"
                                                                class="inline-flex items-center gap-1.5 rounded-lg bg-[var(--color-primary)] px-3 py-1.5 text-xs font-bold text-white transition hover:bg-[var(--color-primary)] disabled:opacity-50">
                                                                Activate
                                                            </button>
                                                        @else
                                                            {{-- REGULAR member → open the shared ActivationReviewModal --}}
                                                            <button
                                                                type="button"
                                                                wire:click="openRegularMemberReview({{ $row['ActivationID'] }})"
                                                                wire:loading.attr="disabled"
                                                                title="Review & Approve Regular Member"
                                                                class="inline-flex items-center gap-1.5 rounded-lg bg-[var(--color-accent)] px-3 py-1.5 text-xs font-bold text-white transition hover:bg-[var(--color-accent)] disabled:opacity-50">
                                                                Approve
                                                            </button>
                                                        @endif
                                                    @endif
                                                @else
                                                    <span class="text-xs font-semibold text-slate-500">—</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <p class="mt-3 text-xs text-slate-500">
                            <span class="font-bold text-[var(--color-primary)]">Free members</span> are activated directly
                            (their membership-fee ledger completes and the satellite owner earns DR / PF,
                            plus TX / OV up the line).
                            <span class="font-bold text-[var(--color-accent-darker)]">Regular members</span> go through the
                            standard review modal — same process as approving any normal member.
                        </p>

                    @else

                        <div class="rounded-xl border border-dashed border-[var(--color-primary-light)] bg-[var(--color-primary-tint)]/50 p-8 text-center">
                            <p class="text-sm font-bold text-[var(--color-primary)]">No members yet</p>
                            <p class="mt-1 text-xs text-slate-500">
                                Free members are created automatically once this satellite's own activation
                                is approved (Bronze 20 · Silver 40 · Gold 80). Regular members appear here
                                when they join using this satellite member's referral code.
                            </p>
                        </div>

                    @endif

                </div>

                {{-- ======================== FOOTER ======================== --}}
                <div class="sticky bottom-0 z-10 flex flex-col gap-3 border-t border-slate-200 bg-white px-5 py-4 shadow-sm sm:flex-row sm:items-center sm:justify-between">

                    <div class="text-xs text-slate-500">
                        @if ($satellite)
                            <span class="font-semibold text-slate-700">{{ $satellite->satellite_name }}</span>
                            &middot; {{ count($freeMembers) }} members
                            &middot; <span class="font-bold text-[var(--color-primary)]">{{ $pendingFreeCount }} free pending</span>
                        @endif
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        @if ($this->canUpdate && $pendingFreeCount > 0)
                            <button
                                type="button"
                                wire:click="confirmApproveAllFreeMembers"
                                wire:loading.attr="disabled"
                                class="rounded-xl bg-[var(--color-primary)] px-4 py-2.5 text-xs font-bold uppercase tracking-wide text-white transition hover:bg-[var(--color-primary-dark)] disabled:opacity-50">
                                Activate All Free ({{ $pendingFreeCount }})
                            </button>
                        @endif

                        <button
                            wire:click="close"
                            type="button"
                            class="rounded-xl border border-slate-200 bg-slate-100 px-4 py-2.5 text-xs font-bold uppercase tracking-wide text-slate-600 transition hover:bg-slate-200">
                            Close
                        </button>
                    </div>
                </div>

            </div>

        </div>

    @endif
</div>
