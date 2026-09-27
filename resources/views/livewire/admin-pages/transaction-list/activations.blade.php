<div class="masterlist-container">

    <div class="members-header px-4 pt-4">
        <div class="members-header-content">
            <h2 class="masterlist-title">Activation History</h2>
            <p class="masterlist-description">Every membership activation and its receipt</p>
        </div>

        <button type="button" wire:click="resetTable" class="masterlist-btn">
            Refresh
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
                        <th>Receipt No.</th>
                        <th>Amount</th>
                        <th>Payment</th>
                        <th>Cashier</th>
                        <th>Status</th>
                        <th>Date</th>
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
                            <td data-label="Receipt No.">{{ $a->receipt_number ?? '—' }}</td>
                            <td data-label="Amount" class="txn-amount">₱{{ number_format((float) $a->amount, 2) }}</td>
                            <td data-label="Payment">{{ ucfirst($a->payment_method ?? '—') }}</td>
                            <td data-label="Cashier">{{ $a->cashier_name ?? '—' }}</td>
                            <td data-label="Status">
                                <span class="txn-pill txn-pill-{{ strtolower($a->status) }}">{{ $a->status }}</span>
                            </td>
                            <td data-label="Date">{{ $a->created_at?->format('M d, Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="txn-empty">No activations found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

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
