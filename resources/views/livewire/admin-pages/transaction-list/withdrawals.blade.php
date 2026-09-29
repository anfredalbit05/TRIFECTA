<div class="masterlist-container">

    <div class="members-header px-4 pt-4">
        <div class="members-header-content">
            <h2 class="masterlist-title">Withdrawal History</h2>
            <p class="masterlist-description">Every withdrawal requested by members</p>
        </div>

        <button
            type="button"
            wire:click="resetTable"
            wire:loading.attr="disabled"
            title="Refresh Table"
            class="refresh-btn"
        >
            {{-- NORMAL STATE --}}
            <span wire:loading.remove wire:target="resetTable" class="refresh-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 4v5h5M20 20v-5h-5M5.5 9A7 7 0 0 1 18 6.5L20 9M18.5 15A7 7 0 0 1 6 17.5L4 15"/>
                </svg>
            </span>

            {{-- LOADING STATE --}}
            <span wire:loading wire:target="resetTable" class="refresh-loading">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="9" stroke-width="3" class="refresh-circle"/>
                    <path d="M21 12a9 9 0 0 1-9 9" stroke-width="3" stroke-linecap="round"/>
                </svg>
            </span>

            <span class="hidden sm:inline">Refresh</span>
        </button>
    </div>

    {{-- SEARCH + (aligned with the Activations toolbar: one flex row) --}}
    <div class="mb-4 flex flex-wrap items-center gap-3 px-4">
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
                        <th>Actions</th>
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
                            <td data-label="Actions" class="txn-actions">
                                @if ($this->canProcess)
                                    <button type="button"
                                        wire:click="reviewWithdrawal({{ $w->WithdrawID }})"
                                        class="txn-action-btn txn-action-review"
                                        title="Review details"
                                        aria-label="Review withdrawal for {{ trim($w->user?->lastname.' '.$w->user?->firstname) ?: 'member' }}">👁</button>
                                @else
                                    <span class="txn-actions-none">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="txn-empty">No withdrawals found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Approve/reject dialogs are handled globally by the page shell —
         see resources/views/pages/admin-pages/transaction-list.blade.php --}}

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
