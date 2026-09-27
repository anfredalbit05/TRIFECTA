<div class="masterlist-container">

    <div class="members-header px-4 pt-4">
        <div class="members-header-content">
            <h2 class="masterlist-title">Withdrawal History</h2>
            <p class="masterlist-description">Every withdrawal requested by members</p>
        </div>

        <button type="button" wire:click="resetTable" class="masterlist-btn">
            Refresh
        </button>
    </div>

    {{-- SEARCH --}}
    <div class="mb-4 px-4">
        <input type="text" wire:model.live.debounce.350ms="search" class="txn-search-input"
            placeholder="Search ID, member, method, reference, status...">
    </div>

    {{-- TABLE --}}
    <div class="px-4 pb-4">
        <div class="txn-table-wrap">
            <table class="txn-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Member</th>
                        <th>Type</th>
                        <th>Requested</th>
                        <th>Net Paid</th>
                        <th>Method</th>
                        <th>Reference</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($withdrawals as $w)
                        <tr wire:key="wd-{{ $w->WithdrawID }}">
                            {{-- Row number generated in the view (continues across pages) --}}
                            <td data-label="#">{{ ($withdrawals->perPage() * ($withdrawals->currentPage() - 1)) + $loop->iteration }}</td>
                            <td data-label="Member">
                                {{ trim($w->user?->lastname.' '.$w->user?->firstname) ?: '—' }}
                            </td>
                            <td data-label="Type">{{ $w->type ?: '—' }}</td>
                            <td data-label="Requested" class="txn-amount">₱{{ number_format((float) $w->requested_amount, 2) }}</td>
                            <td data-label="Net Paid" class="txn-amount">₱{{ number_format((float) $w->amount, 2) }}</td>
                            <td data-label="Method">{{ $w->withdrawal_method ?: '—' }}</td>
                            <td data-label="Reference">{{ $w->payment_reference ?: '—' }}</td>
                            <td data-label="Status">
                                <span class="txn-pill txn-pill-{{ strtolower($w->status) }}">{{ $w->status }}</span>
                            </td>
                            <td data-label="Date">{{ $w->created_at?->format('M d, Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="txn-empty">No withdrawals found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- PAGINATION — same style as Report & Settings tables --}}
    @if ($withdrawals->lastPage() > 1)
        <div class="pagination-wrapper">
            @if ($withdrawals->onFirstPage())
                <span class="pagination-btn-disabled">Previous</span>
            @else
                <button wire:click="previousPage" wire:loading.attr="disabled"
                    class="pagination-btn pagination-btn-prev">Previous</button>
            @endif

            <span class="pagination-label">Page {{ $withdrawals->currentPage() }} of
                {{ $withdrawals->lastPage() }}</span>

            @if ($withdrawals->hasMorePages())
                <button wire:click="nextPage" wire:loading.attr="disabled"
                    class="pagination-btn pagination-btn-next">Next</button>
            @else
                <span class="pagination-btn-disabled">Next</span>
            @endif
        </div>
    @endif

</div>
