<div class="masterlist-container">

    <div class="members-header px-4 pt-4">
        <div class="members-header-content">
            <h2 class="masterlist-title">Activation History</h2>
            <p class="masterlist-description">Every membership activation and its receipt</p>
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

    {{-- SEARCH + STATUS FILTER --}}
    <div class="mb-4 flex flex-wrap items-center gap-3 px-4">
        <input type="text" wire:model.live.debounce.350ms="search" class="txn-search-input"
            placeholder="Search ID, member, receipt no., payment method, status...">

        <select wire:model.live="statusFilter" class="txn-filter-select">
            <option value="">All statuses</option>
            <option value="Pending">Pending</option>
            <option value="Approved">Approved</option>
            <option value="Rejected">Rejected</option>
        </select>
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
                        <th>Receipt No.</th>
                        <th>Amount</th>
                        <th>Payment</th>
                        <th>Cashier</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($activations as $a)
                        <tr wire:key="act-{{ $a->ActivationID }}">
                            {{-- Row number generated in the view (continues across pages) --}}
                            <td data-label="#">{{ ($activations->perPage() * ($activations->currentPage() - 1)) + $loop->iteration }}</td>
                            <td data-label="Member">
                                {{ trim($a->user?->lastname.' '.$a->user?->firstname) ?: '—' }}
                            </td>
                            <td data-label="Type">
                                @if ((int) ($a->user?->RoleID ?? 0) === 8)
                                    <span class="txn-type-pill txn-type-satellite">Satellite</span>
                                @else
                                    <span class="txn-type-pill txn-type-truelife">TrueLife</span>
                                @endif
                            </td>
                            <td data-label="Receipt No.">{{ $a->receipt_number ?? '—' }}</td>
                            <td data-label="Amount" class="txn-amount">₱{{ number_format((float) $a->amount, 2) }}</td>
                            <td data-label="Payment">{{ ucfirst($a->payment_method ?? '—') }}</td>
                            <td data-label="Cashier">{{ $a->cashier_name ?? '—' }}</td>
                            <td data-label="Status">
                                <span class="txn-pill txn-pill-{{ strtolower($a->status) }}">{{ $a->status }}</span>
                            </td>
                            <td data-label="Date">{{ $a->created_at?->format('M d, Y') }}</td>
                            <td data-label="Actions" class="txn-actions">
                                @if ($this->canProcess && in_array($a->status, ['Pending', 'Rejected']))
                                    <button type="button"
                                        wire:click="reviewActivation({{ $a->ActivationID }})"
                                        class="txn-action-btn txn-action-review"
                                        title="Review details"
                                        aria-label="Review activation for {{ trim($a->user?->lastname.' '.$a->user?->firstname) ?: 'member' }}">👁</button>
                                @endif
                                @if (! ($this->canProcess && in_array($a->status, ['Pending', 'Rejected'])))
                                    <span class="txn-actions-none">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="txn-empty">No activations found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Approve/reject dialogs are handled globally by the page shell —
         see resources/views/pages/admin-pages/transaction-list.blade.php --}}

    {{-- PAGINATION — same style as Report & Settings tables --}}
    @if ($activations->lastPage() > 1)
        <div class="pagination-wrapper">
            @if ($activations->onFirstPage())
                <span class="pagination-btn-disabled">Previous</span>
            @else
                <button wire:click="previousPage" wire:loading.attr="disabled"
                    class="pagination-btn pagination-btn-prev">Previous</button>
            @endif

            <span class="pagination-label">Page {{ $activations->currentPage() }} of
                {{ $activations->lastPage() }}</span>

            @if ($activations->hasMorePages())
                <button wire:click="nextPage" wire:loading.attr="disabled"
                    class="pagination-btn pagination-btn-next">Next</button>
            @else
                <span class="pagination-btn-disabled">Next</span>
            @endif
        </div>
    @endif

</div>
