
@php
    $isActive = $user->status === 'Active';
    $canDownloadPolicy = $isActive && $hasApprovedActivation;
@endphp

<div class="account-page">

    {{-- Header --}}
    <div>
        <h2 class="profile-header-title">Membership Policy & Acknowledgement Receipt</h2>
        <p class="profile-header-subtitle">
            Access and download your official membership documents, including your activation receipt and policy agreement.
        </p>
    </div>

    {{-- Membership Information --}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Membership Documents & Guidelines</h3>
            <p class="card-subtitle">
                Important information regarding your membership documents and download eligibility.
            </p>
        </div>

        <div class="policy-list">

            <div class="policy-item">
                <div class="policy-number">1</div>
                <p>
                    <strong class="text-slate-900">Activation Receipt:</strong>
                    Your official acknowledgement receipt becomes available once your membership activation has been approved and a receipt number has been issued.
                </p>
            </div>

            <div class="policy-item">
                <div class="policy-number">2</div>
                <p>
                    <strong class="text-slate-900">Membership Policy:</strong>
                    The policy agreement contains the terms, conditions, and guidelines governing your membership. It is available to members with an <span class="font-semibold text-[var(--color-primary)]">Active</span> account and an approved activation.
                </p>
            </div>

            <div class="policy-item">
                <div class="policy-number">3</div>
                <p>
                    <strong class="text-slate-900">Account Status:</strong>
                    Document download privileges depend on your membership status and activation records. If your account becomes Inactive or Suspended, access to the membership policy will be restricted until your account is reactivated.
                </p>
            </div>

        </div>
    </div>

    {{-- Document Downloads --}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Your Documents</h3>
            <p class="card-subtitle">
                Download your official membership documents below.
            </p>
        </div>

        <div class="doc-grid">

            {{-- Activation Receipt --}}
            <div @class(['doc-card', 'doc-card--locked' => ! $latestApprovedActivation])>

                <div class="doc-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                            d="M9 12h6m-6 3h3M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m6.75 12H8.25m0 0H4.5m3.75 0V9.75M6.75 21h10.5A2.25 2.25 0 0 0 19.5 18.75V6.75A2.25 2.25 0 0 0 17.25 4.5H6.75A2.25 2.25 0 0 0 4.5 6.75v12A2.25 2.25 0 0 0 6.75 21Z" />
                    </svg>
                </div>

                <div class="certificate-title-row">
                    <span class="doc-title">Activation Receipt</span>

                    <span @class([
                        'status-badge-sm',
                        'status-badge--active' => $latestApprovedActivation,
                        'status-badge--inactive' => ! $latestApprovedActivation,
                    ])>
                        {{ $latestApprovedActivation ? 'Available' : 'Unavailable' }}
                    </span>
                </div>

                <p class="doc-desc">
                    @if ($latestApprovedActivation)
                        Your activation has been approved. Download your official acknowledgement receipt.
                    @else
                        Your acknowledgement receipt will become available once your activation is approved and a receipt number is issued.
                    @endif
                </p>

                <div class="doc-foot">
                    @if ($latestApprovedActivation)
                        <a href="{{ route('activation.receipt.download', ['activation' => $latestApprovedActivation->ActivationID]) }}"
                            class="btn-dark">
                            <svg xmlns="http://www.w3.org/2000/svg" class="btn-icon-dark" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            Download Receipt
                        </a>
                    @else
                        <button type="button" disabled class="btn-locked-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="btn-icon-dark" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                            Unavailable
                        </button>
                    @endif
                </div>
            </div>

            {{-- Membership Policy --}}
            <div @class(['doc-card', 'doc-card--locked' => ! $canDownloadPolicy])>

                <div class="doc-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                            d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                    </svg>
                </div>

                <div class="certificate-title-row">
                    <span class="doc-title">Membership Policy</span>

                    <span @class([
                        'status-badge-sm',
                        'status-badge--active' => $canDownloadPolicy,
                        'status-badge--inactive' => ! $canDownloadPolicy,
                    ])>
                        {{ $canDownloadPolicy ? 'Available' : 'Unavailable' }}
                    </span>
                </div>

                <p class="doc-desc">
                    @if ($canDownloadPolicy)
                        Your account is eligible. Download your official membership policy agreement.
                    @elseif (! $isActive)
                        Your account must be Active to download the membership policy.
                    @else
                        Your membership policy will become available once your activation is approved.
                    @endif
                </p>

                <div class="doc-foot">
                    @if ($canDownloadPolicy)
                        <a href="{{ route('policy.download') }}" target="_blank" class="btn-outline">
                            <svg xmlns="http://www.w3.org/2000/svg" class="btn-icon-outline" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Download Policy
                        </a>
                    @else
                        <button type="button" disabled class="btn-locked">
                            <svg xmlns="http://www.w3.org/2000/svg" class="btn-icon-locked" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                            Locked
                        </button>
                    @endif
                </div>
            </div>

        </div>
    </div>

</div>
