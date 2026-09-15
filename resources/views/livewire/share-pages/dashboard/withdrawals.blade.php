<div class="dashboard-grid">

    <!-- =========================
         WITHDRAWAL SIDEBAR
    ========================== -->
    <div class="dashboard-sidebar order-1 lg:order-2">

        <!-- HEADER -->
        <div
            class="flex items-center justify-between bg-white p-4 rounded-xl shadow-sm border border-gray-100"
        >

            <div>
                <h3 class="font-bold text-gray-800 text-base">
                    Withdrawal History
                </h3>

                <p class="text-xs text-gray-500">
                    View your withdrawal transactions.
                </p>
            </div>

            <!-- REFRESH -->
            <button
                type="button"
                wire:click="refreshDashboard"
                wire:loading.attr="disabled"
                title="Refresh Withdrawals"
                class="refresh-btn"
            >

                <!-- NORMAL -->
                <span
                    wire:loading.remove
                    wire:target="refreshDashboard"
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

                <!-- LOADING -->
                <span
                    wire:loading
                    wire:target="refreshDashboard"
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

                <span>
                    Refresh
                </span>

            </button>

        </div>


        <!-- =========================
             TOTAL WITHDRAWALS
        ========================== -->
        <aside class="wallet-card">

            <div class="wallet-block">

                <span class="wallet-label">
                    Total Withdrawals
                </span>

                <h2 class="wallet-value">
                    ₱{{ number_format($totalWithdrawals, 2) }}
                </h2>

                <p class="text-xs text-gray-500 mt-2">
                    Total amount from approved withdrawals.
                </p>

            </div>

        </aside>

    </div>


    <!-- =========================
         LEFT COLUMN
    ========================== -->
    <div class="feed-column order-2 lg:order-1">

        <!-- =========================
             WITHDRAWAL HISTORY
        ========================== -->
        <div class="member-card">

            <div class="card-header">

                <div>
                    <h3 class="font-bold text-gray-800">
                        My Withdrawals
                    </h3>

                    <p class="text-sm text-gray-500">
                        Your withdrawal transaction history.
                    </p>
                </div>

            </div>


            <div class="card-body">

                <div
                    class="withdrawal-history-list"
                    style="
                        max-height: 430px;
                        overflow-y: auto;
                        padding-right: 6px;
                    "
                >

                    @forelse ($withdrawals as $withdrawal)

                        <div
                            wire:key="withdrawal-{{ $withdrawal->id ?? $withdrawal->ID }}"
                            class="flex items-center justify-between gap-4
                                p-4 mb-3 rounded-xl
                                border border-gray-100
                                bg-gray-50"
                        >

                            <!-- LEFT -->
                            <div>

                                <h4 class="font-bold text-gray-800">
                                    ₱{{ number_format($withdrawal->requested_amount, 2) }}
                                </h4>

                                <p class="text-xs text-gray-500 mt-1">
                                    Date Requested -
                                    {{ $withdrawal->created_at
                                        ? $withdrawal->created_at->format('M d, Y h:i A')
                                        : 'Date unavailable'
                                    }}
                                </p>

                            </div>

                            <!-- RIGHT -->
                            <div class="flex flex-col items-end">

                                @php
                                    $status = strtolower(
                                        trim($withdrawal->status ?? 'pending')
                                    );
                                @endphp

                                @if ($status === 'approved')

                                    <span
                                        class="inline-flex items-center
                                            px-3 py-1.5 rounded-full
                                            text-xs font-semibold
                                            bg-[#E8F0FF] text-[#0C2C6B]"
                                    >
                                        Approved
                                    </span>

                                @elseif ($status === 'pending')

                                    <span
                                        class="inline-flex items-center
                                            px-3 py-1.5 rounded-full
                                            text-xs font-semibold
                                            bg-yellow-100 text-yellow-700"
                                    >
                                        Pending
                                    </span>

                                @elseif ($status === 'rejected')

                                    <span
                                        class="inline-flex items-center
                                            px-3 py-1.5 rounded-full
                                            text-xs font-semibold
                                            bg-red-100 text-red-700"
                                    >
                                        Rejected
                                    </span>

                                @elseif ($status === 'completed')

                                    <span
                                        class="inline-flex items-center
                                            px-3 py-1.5 rounded-full
                                            text-xs font-semibold
                                            bg-blue-100 text-blue-700"
                                    >
                                        Completed
                                    </span>

                                @else

                                    <span
                                        class="inline-flex items-center
                                            px-3 py-1.5 rounded-full
                                            text-xs font-semibold
                                            bg-gray-100 text-gray-600"
                                    >
                                        {{ ucfirst($withdrawal->status ?? 'Unknown') }}
                                    </span>

                                @endif

                                <p class="text-xs text-gray-500 mt-1">
                                    {{ $withdrawal->updated_at
                                        ? $withdrawal->updated_at->format('M d, Y h:i A')
                                        : 'Date unavailable'
                                    }}
                                </p>

                            </div>

                        </div>

                    @empty

                        <div class="empty-news">

                            <div class="empty-news-icon">
                                💸
                            </div>

                            <h3>
                                No withdrawals yet
                            </h3>

                            <p>
                                You don't have any withdrawal transactions.
                            </p>

                        </div>

                    @endforelse

                </div>

            </div>

        </div>

    </div>

</div>
