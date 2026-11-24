
<div class="space-y-4">

    {{-- HEADER / EXPORT --}}
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-lg font-bold text-slate-800">
                Cocogen Members Insurance
            </h2>
            <p class="text-sm text-slate-500">
                View and export member insurance records.
            </p>
        </div>

        @if ($this->canRead)
            <button
                type="button"
                wire:click="exportExcel"
                wire:loading.attr="disabled"
                wire:target="exportExcel"
                class="inline-flex items-center gap-2 rounded-xl bg-[var(--color-primary)] px-4 py-2.5 text-sm font-bold text-white shadow-sm transition hover:opacity-90 disabled:cursor-wait disabled:opacity-60"
            >
                <svg
                    wire:loading.remove
                    wire:target="exportExcel"
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-4 w-4"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                >
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                    <path d="m7 10 5 5 5-5"/>
                    <path d="M12 15V3"/>
                </svg>

                <svg
                    wire:loading
                    wire:target="exportExcel"
                    class="h-4 w-4 animate-spin"
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                >
                    <circle
                        class="opacity-25"
                        cx="12"
                        cy="12"
                        r="10"
                        stroke="currentColor"
                        stroke-width="4"
                    />
                    <path
                        class="opacity-75"
                        fill="currentColor"
                        d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"
                    />
                </svg>

                <span wire:loading.remove wire:target="exportExcel">
                    Download Excel
                </span>

                <span wire:loading wire:target="exportExcel">
                    Preparing Excel...
                </span>
            </button>
        @endif
    </div>

    {{-- FILTER BAR --}}
    <div class="flex flex-wrap items-center gap-2">

        <input
            wire:model.live.debounce.400ms="search"
            type="text"
            placeholder="Search member, beneficiary, address, CP # or plan..."
            class="w-full max-w-sm rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)]"
        >

        <select
            wire:model.live="statusFilter"
            class="rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[var(--color-primary)]"
        >
            <option value="">All Statuses</option>

            @foreach ($civilStatuses as $civilStatus)
                <option value="{{ $civilStatus }}">
                    {{ $civilStatus }}
                </option>
            @endforeach
        </select>

        {{-- <button
            type="button"
            wire:click="resetTable"
            class="rounded-xl border border-slate-300 px-4 py-2 text-sm font-bold text-slate-600 transition hover:bg-slate-50"
        >
            Reset
        </button> --}}
    </div>

    {{-- TABLE --}}
    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

        <div class="overflow-x-auto">
            <table class="w-full text-sm">

                <thead>
                    <tr class="bg-slate-50 text-left text-[11px] uppercase tracking-wide text-slate-500">
                        <th class="whitespace-nowrap px-4 py-3 font-bold">
                            No.
                        </th>

                        <th class="whitespace-nowrap px-4 py-3 font-bold">
                            Name
                        </th>

                        <th class="whitespace-nowrap px-4 py-3 font-bold">
                            Name of Beneficiary
                        </th>

                        <th class="whitespace-nowrap px-4 py-3 font-bold">
                            Address
                        </th>

                        <th class="whitespace-nowrap px-4 py-3 font-bold">
                            B-Date
                        </th>

                        <th class="whitespace-nowrap px-4 py-3 font-bold">
                            Status
                        </th>

                        <th class="whitespace-nowrap px-4 py-3 font-bold">
                            Gender
                        </th>

                        <th class="whitespace-nowrap px-4 py-3 font-bold">
                            Occupation
                        </th>

                        <th class="whitespace-nowrap px-4 py-3 font-bold">
                            CP #
                        </th>

                        <th class="whitespace-nowrap px-4 py-3 font-bold">
                            Plan
                        </th>

                        <th class="whitespace-nowrap px-4 py-3 text-right font-bold">
                            Amount
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">

                    @forelse ($fees as $index => $fee)
                        <tr
                            wire:key="cocogen-row-{{ $fee->CocogenID }}"
                            class="transition hover:bg-slate-50/60"
                        >
                            <td class="whitespace-nowrap px-4 py-3 text-slate-500">
                                {{ $fees->firstItem() + $index }}
                            </td>

                            <td class="whitespace-nowrap px-4 py-3 font-semibold text-slate-700">
                                {{ $fee->member_name ?: '—' }}
                            </td>

                            <td class="whitespace-nowrap px-4 py-3 text-slate-600">
                                {{ $fee->beneficiary_name ?: '—' }}
                            </td>

                            <td class="min-w-[220px] px-4 py-3 text-slate-600">
                                {{ $fee->address ?: '—' }}
                            </td>

                            <td class="whitespace-nowrap px-4 py-3 text-slate-600">
                                {{ $fee->birthdate
                                    ? \Illuminate\Support\Carbon::parse($fee->birthdate)->format('d-m-Y')
                                    : '—'
                                }}
                            </td>

                            <td class="whitespace-nowrap px-4 py-3">
                                <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-[11px] font-bold text-slate-700">
                                    {{ $fee->civil_status ?: '—' }}
                                </span>
                            </td>

                            <td class="whitespace-nowrap px-4 py-3 text-slate-600">
                                {{ $fee->gender ?: '—' }}
                            </td>

                            <td class="whitespace-nowrap px-4 py-3 text-slate-600">
                                {{ $fee->occupation ?: '—' }}
                            </td>

                            <td class="whitespace-nowrap px-4 py-3 text-slate-600">
                                {{ $fee->contact_number ?: '—' }}
                            </td>

                            <td class="whitespace-nowrap px-4 py-3 text-slate-600">
                                {{ $fee->plan ?: '—' }}
                            </td>

                            <td class="whitespace-nowrap px-4 py-3 text-right font-bold text-slate-800">
                                ₱{{ number_format((float) $fee->amount, 2) }}
                            </td>
                        </tr>

                    @empty
                        <tr>
                            <td
                                colspan="11"
                                class="px-4 py-10 text-center text-sm text-slate-500"
                            >
                                No Cocogen members insurance records found.
                            </td>
                        </tr>
                    @endforelse

                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        @if ($fees->hasPages())
            <div class="border-t border-slate-100 px-4 py-3">
                {{ $fees->links() }}
            </div>
        @endif

    </div>
</div>
