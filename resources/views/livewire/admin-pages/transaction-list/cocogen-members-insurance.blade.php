
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

        {{-- @if ($this->canRead) --}}
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
        {{-- @endif --}}
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
                        <th class="whitespace-nowrap px-4 py-3 text-center font-bold">
                            Action
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
                            <td class="whitespace-nowrap px-4 py-3 text-center">
                                    <button
                                        type="button"
                                        wire:click="openBeneficiaryModal({{ $fee->UserID }})"
                                        wire:loading.attr="disabled"
                                        wire:target="openBeneficiaryModal({{ $fee->UserID }})"
                                        class="inline-flex items-center gap-2 rounded-lg bg-[var(--color-primary)] px-3 py-2 text-xs font-bold text-white transition hover:opacity-90 disabled:opacity-50"
                                    >
                                        <svg
                                            class="h-4 w-4"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M12 5v14m-7-7h14"
                                            />
                                        </svg>

                                        Beneficiaries
                                    </button>
                                    <button
                                        type="button"
                                        wire:click="openOccupationModal({{ $fee->UserID }})"
                                        wire:loading.attr="disabled"
                                        class="inline-flex items-center gap-2 rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs font-bold text-slate-700 transition hover:bg-slate-50 disabled:opacity-50"
                                    >
                                        <svg
                                            class="h-4 w-4"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M12 20h9M16.5 3.5a2.121 2.121 0 013 3L8 18l-4 1 1-4 11.5-11.5z"
                                            />
                                        </svg>

                                        Occupation
                                    </button>
                                </td>
                        </tr>

                    @empty
                        <tr>
                            <td
                                colspan="12"
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

{{-- BENEFICIARY SELECTION MODAL --}}
@if ($showBeneficiaryModal)
    <div
        class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/60 p-4"
        wire:key="beneficiary-selection-modal"
    >
        <div class="w-full max-w-2xl overflow-hidden rounded-2xl bg-white shadow-2xl">

            {{-- HEADER --}}
            <div class="flex items-center justify-between border-b border-slate-200 px-6 py-4">
                <div>
                    <h3 class="text-lg font-bold text-slate-800">
                        Add / Update Beneficiaries
                    </h3>

                    <p class="mt-1 text-sm text-slate-500">
                        Select beneficiaries for:
                        <span class="font-bold text-slate-700">
                            {{ $selectedMemberName }}
                        </span>
                    </p>
                </div>

                <button
                    type="button"
                    wire:click="closeBeneficiaryModal"
                    class="rounded-lg p-2 text-slate-400 transition hover:bg-slate-100 hover:text-slate-700"
                >
                    ✕
                </button>
            </div>

            {{-- BODY --}}
            <div class="max-h-[65vh] space-y-4 overflow-y-auto p-6">

                <div class="rounded-xl border border-blue-100 bg-blue-50 p-3">
                    <p class="text-sm text-blue-800">
                       Select only ONE beneficiary for this Cocogen insurance record.
                        The previously saved beneficiary is automatically selected.
                    </p>
                </div>

                {{-- SELECTION COUNT --}}
                <div class="flex items-center justify-between">
                    <span class="text-sm font-semibold text-slate-600">
                        Available Beneficiaries
                    </span>

                    <span class="rounded-full bg-[var(--color-primary)] px-3 py-1 text-xs font-bold text-white">
                        {{ $selectedBeneficiaryId !== null ? '1 Selected' : '0 Selected' }}
                    </span>
                </div>

                {{-- BENEFICIARY LIST --}}
                <div class="overflow-hidden rounded-xl border border-slate-200">

                    @forelse ($availableBeneficiaries as $beneficiary)

                        <label
                            wire:key="beneficiary-option-{{ $beneficiary['BeneficiaryID'] }}"
                            class="flex cursor-pointer items-start gap-3 border-b border-slate-100 p-4 transition last:border-0 hover:bg-slate-50
                            {{ $selectedBeneficiaryId === (int) $beneficiary['BeneficiaryID'] ? 'bg-blue-50' : '' }}"
                        >

                            <input
                                type="radio"
                                wire:model.live.number="selectedBeneficiaryId"
                                value="{{ $beneficiary['BeneficiaryID'] }}"
                                class="mt-1 h-4 w-4 border-slate-300 text-[var(--color-primary)] focus:ring-[var(--color-primary)]"
                            >

                            <div class="min-w-0 flex-1">

                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="font-bold text-slate-800">
                                        {{ $beneficiary['fullname'] }}
                                    </span>

                                    <span class="rounded-full bg-green-100 px-2 py-0.5 text-[10px] font-bold text-green-700">
                                        Active
                                    </span>
                                </div>

                                <div class="mt-1 flex flex-wrap gap-x-4 gap-y-1 text-xs text-slate-500">
                                    <span>
                                        Relationship:
                                        {{ $beneficiary['relationship'] ?: '—' }}
                                    </span>

                                    <span>
                                        Birthdate:
                                        {{ $beneficiary['birthdate']
                                            ? \Illuminate\Support\Carbon::parse($beneficiary['birthdate'])->format('d-m-Y')
                                            : '—'
                                        }}
                                    </span>
                                </div>

                            </div>

                            @if ($selectedBeneficiaryId === (int) $beneficiary['BeneficiaryID'])
                                <span class="text-xs font-bold text-blue-600">
                                    Selected
                                </span>
                            @endif

                        </label>

                    @empty

                        <div class="px-4 py-10 text-center">
                            <p class="font-semibold text-slate-600">
                                No active beneficiaries found.
                            </p>

                            <p class="mt-1 text-xs text-slate-400">
                                This member does not have any active registered beneficiaries.
                            </p>
                        </div>

                    @endforelse

                </div>

                @error('selectedBeneficiaryId')
                    <p class="text-sm font-medium text-red-600">
                        {{ $message }}
                    </p>
                @enderror

            {{-- SELECTED BENEFICIARY --}}
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">

                <p class="mb-2 text-xs font-bold uppercase tracking-wide text-slate-500">
                    Selected Beneficiary
                </p>

                @php
                    $selectedBeneficiary = collect($availableBeneficiaries)
                        ->firstWhere('BeneficiaryID', $selectedBeneficiaryId);
                @endphp

                @if ($selectedBeneficiary)
                    <div class="rounded-lg bg-white px-3 py-2 shadow-sm">
                        <p class="text-sm font-bold text-slate-800">
                            {{ $selectedBeneficiary['fullname'] }}
                        </p>

                        <p class="mt-1 text-xs text-slate-500">
                            {{ $selectedBeneficiary['relationship'] ?: '—' }}
                        </p>
                    </div>
                @else
                    <p class="text-sm text-slate-400">
                        No beneficiary selected.
                    </p>
                @endif

            </div>
            </div>

            {{-- FOOTER --}}
            <div class="flex flex-wrap items-center justify-end gap-2 border-t border-slate-200 bg-slate-50 px-6 py-4">

                <button
                    type="button"
                    wire:click="closeBeneficiaryModal"
                    class="rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-bold text-slate-600 transition hover:bg-slate-100"
                >
                    Cancel
                </button>

                <button
                    type="button"
                    wire:click="saveBeneficiaries"
                    wire:loading.attr="disabled"
                    wire:target="saveBeneficiaries"
                    class="rounded-xl bg-[var(--color-primary)] px-5 py-2.5 text-sm font-bold text-white shadow-sm transition hover:opacity-90 disabled:cursor-wait disabled:opacity-50"
                >
                    <span wire:loading.remove wire:target="saveBeneficiaries">
                        Save Beneficiaries
                    </span>

                    <span wire:loading wire:target="saveBeneficiaries">
                        Saving...
                    </span>
                </button>

            </div>
        </div>
    </div>
@endif

{{-- OCCUPATION MODAL --}}
@if ($showOccupationModal)
    <div class="fixed inset-0 z-[110] flex items-center justify-center bg-slate-900/60 p-4">

        <div class="w-full max-w-lg overflow-hidden rounded-2xl bg-white shadow-2xl">

            {{-- HEADER --}}
            <div class="flex items-center justify-between border-b border-slate-200 px-6 py-4">
                <div>
                    <h3 class="text-lg font-bold text-slate-800">
                        Add / Update Occupation
                    </h3>

                    <p class="mt-1 text-sm text-slate-500">
                        {{ $selectedOccupationMemberName }}
                    </p>
                </div>

                <button
                    type="button"
                    wire:click="closeOccupationModal"
                    class="rounded-lg p-2 text-slate-400 transition hover:bg-slate-100 hover:text-slate-700"
                >
                    ✕
                </button>
            </div>

            {{-- BODY --}}
            <div class="space-y-4 p-6">

                <div>
                    <label class="mb-2 block text-sm font-bold text-slate-700">
                        Occupation
                    </label>

                    <input
                        type="text"
                        wire:model="occupationInput"
                        placeholder="Enter occupation..."
                        maxlength="255"
                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-[var(--color-primary)] focus:ring-2 focus:ring-[var(--color-primary)]/20"
                    >

                    @error('occupationInput')
                        <p class="mt-1 text-xs font-medium text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

            </div>

            {{-- FOOTER --}}
            <div class="flex justify-end gap-2 border-t border-slate-200 bg-slate-50 px-6 py-4">

                <button
                    type="button"
                    wire:click="closeOccupationModal"
                    class="rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-bold text-slate-600 transition hover:bg-slate-100"
                >
                    Cancel
                </button>

                <button
                    type="button"
                    wire:click="saveOccupation"
                    wire:loading.attr="disabled"
                    wire:target="saveOccupation"
                    class="rounded-xl bg-[var(--color-primary)] px-5 py-2.5 text-sm font-bold text-white shadow-sm transition hover:opacity-90 disabled:opacity-50"
                >
                    <span wire:loading.remove wire:target="saveOccupation">
                        Save Occupation
                    </span>

                    <span wire:loading wire:target="saveOccupation">
                        Saving...
                    </span>
                </button>

            </div>

        </div>
    </div>
@endif

</div>
