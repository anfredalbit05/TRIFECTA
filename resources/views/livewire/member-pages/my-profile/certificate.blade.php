{{--
=====================================================================
 POLICY & CERTIFICATES PAGE
=====================================================================
 Styling lives in my-profile.css (section 13).
 Sections:
   1. Header
   2. Membership & certificate policy card
   3. Document download cards (certificate / receipt / policy)
=====================================================================
--}}

@php
    $status = auth()->user()->status;
    $isActive = $status === 'Active';
@endphp

<div class="account-page">

    {{-- ============================= 1. HEADER ============================= --}}
    <div>
        <h2 class="profile-header-title">Policy & Acknowledegment Receipt</h2>
        <p class="profile-header-subtitle">
            View your policy guidelines, terms, and download your official documents.
        </p>
    </div>

    {{-- =============== 2. MEMBERSHIP & CERTIFICATE POLICY CARD =============== --}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Membership &amp; Acknowledgement Receipt, Policy</h3>
            <p class="card-subtitle">Please review the guidelines regarding certificate issuance and account status.</p>
        </div>

        <div class="policy-list">
            <div class="policy-item">
                <div class="policy-number">1</div>
                <p>
                    <strong class="text-slate-900">Active Status Requirement:</strong> Official certificates (such as the Certificate of Membership or Recognition) are exclusively available for download to users who maintain an <span class="font-semibold text-[var(--color-primary)]">Active</span> account status.
                </p>
            </div>

            <div class="policy-item">
                <div class="policy-number">2</div>
                <p>
                    <strong class="text-slate-900">Validity &amp; Compliance:</strong> Downloaded certificates contain secure reference identifiers linked directly to your account profile. Tampering with or altering digital certificate copies violates system policies.
                </p>
            </div>

            <div class="policy-item">
                <div class="policy-number">3</div>
                <p>
                    <strong class="text-slate-900">Account Suspension:</strong> If an account status changes to <span class="font-semibold text-amber-600">Inactive</span> or <span class="font-semibold text-red-600">Suspended</span>, download privileges are automatically revoked until compliance or activation requirements are fulfilled.
                </p>
            </div>
        </div>
    </div>

    {{-- =============== 3. DOCUMENT DOWNLOAD CARDS =============== --}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Your Documents</h3>
            <p class="card-subtitle">Claim and download your official copies here.</p>
        </div>

        <div class="doc-grid">

            {{-- Certificate of Membership --}}
            {{-- <div @class(['doc-card', 'doc-card--locked' => ! $isActive])>
                <div class="doc-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12.75 11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 0 1-1.043 3.296 3.745 3.745 0 0 1-3.296 1.043A3.745 3.745 0 0 1 12 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 0 1-3.296-1.043 3.745 3.745 0 0 1-1.043-3.296A3.745 3.745 0 0 1 3 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 0 1 1.043-3.296 3.746 3.746 0 0 1 3.296-1.043A3.746 3.746 0 0 1 12 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 0 1 3.296 1.043 3.746 3.746 0 0 1 1.043 3.296A3.745 3.745 0 0 1 21 12Z" />
                    </svg>
                </div>

                <div class="certificate-title-row">
                    <span class="doc-title">Certificate of Membership</span>
                    <span @class([
                        'status-badge-sm',
                        'status-badge--active' => $isActive,
                        'status-badge--inactive' => ! $isActive,
                    ])>{{ $status }}</span>
                </div>

                <p class="doc-desc">
                    @if ($isActive)
                        Your account qualifies. Download your official certificate copy.
                    @else
                        Your account status must be Active to unlock this certificate.
                    @endif
                </p>

                <div class="doc-foot">
                    @if ($isActive)
                        <a href="{{ route('certificate.download') }}" class="btn-dark">
                            <svg xmlns="http://www.w3.org/2000/svg" class="btn-icon-dark" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            Download
                        </a>
                    @else
                        <button type="button" disabled class="btn-locked-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="btn-icon-dark" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                            Locked
                        </button>
                    @endif
                </div>
            </div> --}}

            {{-- Activation Receipt --}}
            <div @class(['doc-card', 'doc-card--locked' => ! $latestApprovedActivation])>
                <div class="doc-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 3h3M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m6.75 12H8.25m0 0H4.5m3.75 0V9.75M6.75 21h10.5A2.25 2.25 0 0 0 19.5 18.75V6.75A2.25 2.25 0 0 0 17.25 4.5H6.75A2.25 2.25 0 0 0 4.5 6.75v12A2.25 2.25 0 0 0 6.75 21Z" />
                    </svg>
                </div>

                <div class="certificate-title-row">
                    <span class="doc-title">Activation Receipt</span>
                    <span @class([
                        'status-badge-sm',
                        'status-badge--active' => $latestApprovedActivation,
                        'status-badge--inactive' => ! $latestApprovedActivation,
                    ])>{{ $latestApprovedActivation ? 'Available' : 'Unavailable' }}</span>
                </div>

                <p class="doc-desc">
                    @if ($latestApprovedActivation)
                        Your membership activation has been approved. Download your official receipt.
                    @else
                        A receipt will appear here once your membership activation has been approved.
                    @endif
                </p>

                <div class="doc-foot">
                    @if ($latestApprovedActivation)
                        <a
                            href="{{ route('activation.receipt.download', ['activation' => $latestApprovedActivation->ActivationID]) }}"
                            class="btn-dark"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="btn-icon-dark" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            Download
                        </a>
                    @else
                        <button type="button" disabled class="btn-locked-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="btn-icon-dark" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                            Unavailable
                        </button>
                    @endif
                </div>
            </div>

            {{-- Policy Document --}}
            {{-- <div @class(['doc-card', 'doc-card--locked' => ! $isActive])>
                <div class="doc-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                    </svg>
                </div>

                <div class="certificate-title-row">
                    <span class="doc-title">Membership Policy</span>
                    <span class="status-badge-sm status-badge--active">Guidelines</span>
                </div>

                <p class="doc-desc">
                    The terms, rules and certificate issuance guidelines that apply to your membership.
                </p>

                <div class="doc-foot">
                    @if ($isActive)
                        <a href="{{ route('policy.download') }}" target="_blank" class="btn-outline">
                            <svg xmlns="http://www.w3.org/2000/svg" class="btn-icon-outline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            View policy
                        </a>
                    @else
                        <button type="button" disabled class="btn-locked">
                            <svg xmlns="http://www.w3.org/2000/svg" class="btn-icon-locked" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                            Locked
                        </button>
                    @endif
                </div>
            </div> --}}

        </div>
    </div>

</div>
