{{--
=====================================================================
 FREE MEMBERS MODAL
=====================================================================
 Backing component:
 App\Livewire\AdminPages\Masterlist\SatelliteMembersComponents\FreeMembersModal
 Opened via the 'open-free-members' event (dispatched with UserID).

 Lists every free member provisioned under one satellite member
 (activations.isFree = true, matched through the satellite's satelliteID)
 and lets an admin activate them — one at a time or all at once.

 Approval is a one-click confirm (no policy / receipt numbers): the shared
 ActivationApprovalService completes the member's Pending membership-fee
 ledger and pays the satellite owner DR / TX / OV / PF.

 Layout & color palette matched to the other satellite member modals
 (rounded-2xl, navy #0B2A5B + gold #E8B923, fixed header/footer with a
 scrolling body).
====================================================================
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
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-[#0B2A5B] text-lg font-extrabold text-[#E8B923]">
                            F
                        </div>
                        <div>
                            <h2 class="text-lg font-extrabold text-[#0B2A5B] sm:text-xl">
                                Free Members
                            </h2>
                            <div class="mt-0.5 flex flex-wrap items-center gap-2 text-xs text-slate-500">
                                <span class="font-semibold text-slate-700">
                                    {{ $satellite?->satellite_name }}
                                </span>

                                @if ($satellite)
                                    <span class="inline-flex rounded-full bg-[#E8B923]/15 px-2.5 py-0.5 text-[10px] font-bold uppercase text-[#8a6d0b]">
                                        {{ config('satellite.tier_by_type')[(int) $satellite->satellite_typeID] ?? 'Satellite' }}
                                    </span>
                                @endif

                                <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 px-2.5 py-0.5 text-[10px] font-bold uppercase text-amber-700">
                                    {{ $pendingCount }} pending
                                </span>

                                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-0.5 text-[10px] font-bold uppercase text-emerald-700">
                                    {{ $approvedCount }} active
                                </span>
                            </div>
                        </div>
                    </div>

                    <button
                        wire:click="close"
                        type="button"
                        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-slate-100 font-bold text-slate-500 transition hover:bg-rose-100 hover:text-rose-600"
                        title="Close">
                        &#10005;
                    </button>
                </div>

                {{-- ======================== BODY ======================== --}}
                <div class="overflow-y-auto p-4 sm:p-6">

                    {{-- SUMMARY --}}
                    <div class="mb-4 grid grid-cols-3 gap-3">
                        <div class="rounded-xl border border-slate-200 bg-slate-50 p-3 text-center">
                            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total</p>
                            <p class="text-xl font-extrabold text-[#0B2A5B]">{{ count($freeMembers) }}</p>
                        </div>
                        <div class="rounded-xl border border-amber-200 bg-amber-50 p-3 text-center">
                            <p class="text-[10px] font-bold uppercase tracking-wider text-amber-600">Pending</p>
                            <p class="text-xl font-extrabold text-amber-700">{{ $pendingCount }}</p>
                        </div>
                        <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-3 text-center">
                            <p class="text-[10px] font-bold uppercase tracking-wider text-emerald-600">Approved</p>
                            <p class="text-xl font-extrabold text-emerald-700">{{ $approvedCount }}</p>
                        </div>
                    </div>

                    {{-- FREE MEMBERS TABLE --}}
                    @if (count($freeMembers))

                        <div class="overflow-x-auto rounded-xl border border-slate-200">
                            <table class="min-w-full divide-y divide-slate-200 text-sm">
                                <thead class="bg-[#0B2A5B]">
                                    <tr class="text-left text-[11px] uppercase tracking-wider text-white">
                                        <th class="px-3 py-3 font-bold">#</th>
                                        <th class="px-3 py-3 font-bold">Username</th>
                                        <th class="px-3 py-3 font-bold">Name</th>
                                        <th class="px-3 py-3 font-bold">Account</th>
                                        <th class="px-3 py-3 font-bold">Activation</th>
                                        <th class="px-3 py-3 text-right font-bold">Amount</th>
                                        <th class="px-3 py-3 text-center font-bold">Action</th>
                                    </tr>
                                </thead>

                                <tbody class="divide-y divide-slate-100 bg-white">
                                    @foreach ($freeMembers as $index => $row)
                                        <tr class="{{ $row['status'] === 'Pending' ? 'bg-amber-50/40' : '' }} transition-colors hover:bg-slate-50">
                                            <td class="px-3 py-2.5 font-semibold text-slate-400">
                                                {{ $index + 1 }}
                                            </td>

                                            <td class="px-3 py-2.5 font-medium text-slate-800">
                                                {{ $row['username'] }}
                                            </td>

                                            <td class="px-3 py-2.5 text-slate-700">
                                                {{ $row['name'] }}
                                            </td>

                                            <td class="px-3 py-2.5">
                                                <span class="inline-flex rounded-full px-2.5 py-0.5 text-[10px] font-bold uppercase {{ $row['user_status'] === 'Active' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                                    {{ $row['user_status'] ?: 'Inactive' }}
                                                </span>
                                            </td>

                                            <td class="px-3 py-2.5">
                                                <span class="inline-flex rounded-full px-2.5 py-0.5 text-[10px] font-bold uppercase {{ $row['status'] === 'Approved' ? 'bg-emerald-100 text-emerald-700' : ($row['status'] === 'Pending' ? 'bg-amber-100 text-amber-700' : 'bg-rose-100 text-rose-700') }}">
                                                    {{ $row['status'] }}
                                                </span>
                                            </td>

                                            <td class="px-3 py-2.5 text-right font-semibold text-slate-700">
                                                ₱{{ number_format($row['amount'], 2) }}
                                            </td>

                                            <td class="px-3 py-2.5 text-center">
                                                @if ($row['status'] === 'Pending')
                                                    @if ($this->canUpdate)
                                                        <button
                                                            type="button"
                                                            wire:click="confirmApproveFreeMember({{ $row['ActivationID'] }})"
                                                            wire:loading.attr="disabled"
                                                            title="Activate Free Member"
                                                            class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-bold text-white transition hover:bg-emerald-700 disabled:opacity-50">
                                                            Activate
                                                        </button>
                                                    @endif
                                                @else
                                                    <span class="text-xs font-semibold text-slate-400">—</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <p class="mt-3 text-xs text-slate-500">
                            Activating a free member completes their membership-fee ledger and credits the
                            satellite owner with the normal DR / PF (plus TX / OV up the line) share.
                        </p>

                    @else

                        <div class="rounded-xl border border-dashed border-slate-300 bg-slate-50 p-8 text-center">
                            <p class="text-sm font-bold text-slate-600">No free members yet</p>
                            <p class="mt-1 text-xs text-slate-500">
                                Free members are created automatically once this satellite's own activation
                                is approved (Bronze 20 · Silver 40 · Gold 80).
                            </p>
                        </div>

                    @endif

                </div>

                {{-- ======================== FOOTER ======================== --}}
                <div class="sticky bottom-0 z-10 flex flex-col gap-3 border-t border-slate-200 bg-white px-5 py-4 shadow-sm sm:flex-row sm:items-center sm:justify-between">

                    <div class="text-xs text-slate-500">
                        @if ($satellite)
                            <span class="font-semibold text-slate-700">{{ $satellite->satellite_name }}</span>
                            &middot; {{ count($freeMembers) }} free members
                        @endif
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        @if ($this->canUpdate && $pendingCount > 0)
                            <button
                                type="button"
                                wire:click="confirmApproveAllFreeMembers"
                                wire:loading.attr="disabled"
                                class="rounded-xl bg-[#0B2A5B] px-4 py-2.5 text-xs font-bold uppercase tracking-wide text-white transition hover:bg-[#12397a] disabled:opacity-50">
                                Activate All ({{ $pendingCount }})
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
