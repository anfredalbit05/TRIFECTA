<div class="masterlist-container">

    <div class="members-header px-4 pt-4">
        <div class="members-header-content">
            <h2 class="masterlist-title">Satellite</h2>
            <p class="masterlist-description">Your satellite memberships and satellites under your referral code</p>
        </div>
    </div>

    {{-- TAB NAV + APPLY ACTION --}}
    <div class="px-4 pt-4 flex items-start justify-between gap-3">
        <div class="sat-tabs">
            <button type="button" wire:click="setTab('overview')"
                class="sat-tab {{ $activeTab === 'overview' ? 'sat-tab--active' : '' }}">
                ⌂ My Satellites
            </button>

            <button type="button" wire:click="setTab('downlines')"
                class="sat-tab {{ $activeTab === 'downlines' ? 'sat-tab--active' : '' }}">
                ⇊ Satellites Under Me
                @if ($this->hasDownlineSatellites)
                    <span class="sat-tab-count">{{ $this->downlineSatellites->count() }}</span>
                @endif
            </button>
        </div>

        {{-- Apply for a new satellite membership — opens the SAME
             SatelliteModal wizard the admin Members page uses. --}}
        <button type="button" wire:click="$dispatch('self-apply-satellite')"
            class="sat-apply-btn" title="Apply for a new satellite membership">
            ＋ Apply Satellite
        </button>
    </div>

    {{-- ========================= MY SATELLITES ========================= --}}
    @if ($activeTab === 'overview')
        <div class="px-4 pb-6 pt-4">
            <div class="sat-table-wrap">
                <table class="sat-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Satellite Name</th>
                            <th>Plan</th>
                            <th>Address</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Applied</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($this->mySatellites as $i => $satellite)
                            <tr wire:key="sat-{{ $satellite->satelliteID }}">
                                <td data-label="#">{{ $i + 1 }}</td>
                                <td data-label="Satellite Name">{{ $satellite->satellite_name }}</td>
                                <td data-label="Plan">{{ $satellite->tier }}</td>
                                <td data-label="Address">{{ $satellite->address ?: '—' }}</td>
                                <td data-label="Amount">₱{{ number_format($satellite->amount, 2) }}</td>
                                <td data-label="Status">
                                    <span class="sat-pill sat-pill-{{ strtolower($satellite->activation_status) }}">
                                        {{ $satellite->activation_status }}
                                    </span>
                                </td>
                                <td data-label="Applied">{{ $satellite->applied_at?->format('M d, Y') ?? '—' }}</td>
                                <td data-label="Actions">
                                    <div class="flex gap-1">
                                        <button type="button" wire:click="$dispatch('self-view-satellite', { userId: {{ $satellite->UserID }} })"
                                            class="sat-action" title="View">
                                            👁
                                        </button>

                                        @if ($satellite->activation_status === 'Pending')
                                            <button type="button"
                                                wire:click="$dispatch('self-review-satellite', { userId: {{ $satellite->UserID }} })"
                                                class="sat-action" title="Payment review">
                                                ⟳
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="sat-empty">
                                    No satellite applications yet. An administrator can file one for you,
                                    or one of your uplines can apply using your account.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- ====================== SATELLITES UNDER ME ====================== --}}
    @if ($activeTab === 'downlines')
        <div class="px-4 pb-6 pt-4">
            <div class="sat-table-wrap">
                <table class="sat-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Satellite Member</th>
                            <th>Satellite Name</th>
                            <th>Plan</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Applied</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($this->downlineSatellites as $i => $d)
                            <tr wire:key="satd-{{ $d->activation_id }}">
                                <td data-label="#">{{ $i + 1 }}</td>
                                <td data-label="Satellite Member">
                                    {{ $d->name }}
                                    <span class="block text-xs text-slate-400">{{ $d->username }}</span>
                                </td>
                                <td data-label="Satellite Name">{{ $d->satellite_name }}</td>
                                <td data-label="Plan">{{ $d->tier }}</td>
                                <td data-label="Amount">₱{{ number_format($d->amount, 2) }}</td>
                                <td data-label="Status">
                                    <span class="sat-pill sat-pill-{{ strtolower($d->status) }}">
                                        {{ $d->status }}
                                    </span>
                                </td>
                                <td data-label="Applied">{{ $d->applied_at?->format('M d, Y') ?? '—' }}</td>

                                {{-- View opens the SAME shared view modal the admin page uses --}}
                                <td data-label="Actions">
                                    <div class="flex gap-1">
                                        <button type="button"
                                            wire:click="$dispatch('self-view-satellite', { userId: {{ $d->user_id }} })"
                                            class="sat-action" title="View">
                                            👁
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="sat-empty">
                                    No satellite members have used your referral code as their upline yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- ============================================================ --}}
    {{-- SHARED MODALS — the SAME components the admin page mounts --}}
    {{-- ============================================================ --}}
    <livewire:admin-pages.masterlist.satellite-members-components.satellite-members-view />
    <livewire:admin-pages.masterlist.satellite-members-components.satellite-members-activation-review />

    {{-- Apply wizard — the SAME component the admin Members page mounts --}}
    <livewire:admin-pages.masterlist.members-components.satellite-modal />

</div>
