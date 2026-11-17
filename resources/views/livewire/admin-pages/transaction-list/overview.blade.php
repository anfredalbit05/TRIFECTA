<div class="masterlist-container">

    <div class="masterlist-header">
        <div>
            <h2 class="masterlist-title">Transactions Overview</h2>
            <p class="masterlist-description">Quick totals for withdrawals and activations</p>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 p-4 sm:grid-cols-2 lg:grid-cols-4">

        {{-- Withdrawal cards --}}
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Withdrawals</p>
            <p class="mt-2 text-3xl font-bold text-[var(--color-primary)]">{{ number_format($withdrawalCount) }}</p>
            <p class="mt-1 text-xs text-slate-500">{{ number_format($withdrawalApproved) }} approved</p>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Withdrawn Total</p>
            <p class="mt-2 text-3xl font-bold text-[var(--color-primary)]">₱{{ number_format($withdrawalTotal, 2) }}</p>
            <p class="mt-1 text-xs text-slate-500">net of tax, approved only</p>
        </div>

        {{-- Activation cards --}}
        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Activations</p>
            <p class="mt-2 text-3xl font-bold text-[var(--color-primary)]">{{ number_format($activationCount) }}</p>
            <p class="mt-1 text-xs text-slate-500">
                {{ number_format($activationApproved) }} approved · {{ number_format($activationPending) }} pending · {{ number_format($activationRejected) }} rejected
            </p>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Activation Total</p>
            <p class="mt-2 text-3xl font-bold text-[var(--color-primary)]">₱{{ number_format($activationTotal, 2) }}</p>
            <p class="mt-1 text-xs text-slate-500">approved memberships</p>
        </div>

    </div>

    <div class="p-4">
        <div class="overflow-x-auto rounded-xl border border-slate-200 bg-white shadow-sm">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-[var(--color-primary)] text-left text-xs uppercase tracking-wider text-white">
                        <th class="px-4 py-3">Month</th>
                        <th class="px-4 py-3">Approved Activations</th>
                        <th class="px-4 py-3">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($monthlyActivations as $row)
                        <tr class="border-t border-slate-100 hover:bg-slate-50">
                            <td class="px-4 py-3 font-semibold text-slate-700">{{ $row->month }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ number_format($row->total) }}</td>
                            <td class="px-4 py-3 font-semibold text-slate-700">₱{{ number_format((float) $row->amount, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-4 py-6 text-center text-slate-500">No activation data for the last 6 months.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
