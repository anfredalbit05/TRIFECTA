{{--
=====================================================================
 POLICY & CERTIFICATES PAGE
=====================================================================
 Styling lives in profile.css (section 13 — reuses shared classes
 from earlier sections, e.g. .card, .card-header, .status-badge).
 Sections:
   1. Header
   2. Membership & Certificate Policy card
   3. Certificate Download Portal card
=====================================================================
--}}

<div class="account-page">

    {{-- ============================= 1. HEADER ============================= --}}
    <div>
        <h2 class="profile-header-title">Policy & Certificates</h2>
        <p class="profile-header-subtitle">
            View your policy guidelines, terms, and download your official documents.
        </p>
    </div>

    {{-- =============== 2. MEMBERSHIP & CERTIFICATE POLICY CARD =============== --}}
    <div class="card">
        <div class="card-header-row">
            <div>
                <h3 class="card-title">Membership & Certificate Policy</h3>
                <p class="card-subtitle">Please review the guidelines regarding certificate issuance and account status.</p>
            </div>

            <div class="card-header-action">
                @if(auth()->user()->status === 'Active')
                    <a href="{{ route('policy.download') }}" target="_blank" class="btn-outline">
                        <svg xmlns="http://www.w3.org/2000/svg" class="btn-icon-outline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Download Policy File
                    </a>
                @else
                    <button type="button" disabled class="btn-locked">
                        <svg xmlns="http://www.w3.org/2000/svg" class="btn-icon-locked" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        Policy Locked (Active Status Required)
                    </button>
                @endif
            </div>
        </div>

        <div class="policy-list">
            <div class="policy-item">
                <div class="policy-number">1</div>
                <p>
                    <strong class="text-slate-900">Active Status Requirement:</strong> Official certificates (such as the Certificate of Membership or Recognition) are exclusively available for download to users who maintain an <span class="font-semibold text-emerald-600">Active</span> account status.
                </p>
            </div>

            <div class="policy-item">
                <div class="policy-number">2</div>
                <p>
                    <strong class="text-slate-900">Validity & Compliance:</strong> Downloaded certificates contain secure reference identifiers linked directly to your account profile. Tampering with or altering digital certificate copies violates system policies.
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

    {{-- =============== 3. CERTIFICATE DOWNLOAD PORTAL CARD =============== --}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Certificate Download Portal</h3>
            <p class="card-subtitle">Claim and download your official document copy here.</p>
        </div>

        <div class="certificate-row">
            <div class="certificate-info">
                <div class="certificate-title-row">
                    <span class="card-title">Official Status Certificate</span>
                    <span @class([
                        'status-badge-sm',
                        'status-badge--active' => auth()->user()->status === 'Active',
                        'status-badge--inactive' => auth()->user()->status === 'Inactive',
                        'status-badge--suspended' => auth()->user()->status === 'Suspended',
                    ])>
                        {{ auth()->user()->status }}
                    </span>
                </div>
                <p class="certificate-desc">
                    @if(auth()->user()->status === 'Active')
                        Your account qualifies. You can securely claim and download your document.
                    @else
                        Your account status must be Active to unlock this certificate file.
                    @endif
                </p>
            </div>

            <div class="card-header-action">
                @if(auth()->user()->status === 'Active')
                    <a href="{{ route('certificate.download') }}" class="btn-dark">
                        <svg xmlns="http://www.w3.org/2000/svg" class="btn-icon-dark" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        Download Certificate File
                    </a>
                @else
                    <button type="button" disabled class="btn-locked-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="btn-icon-dark" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        Locked (Active Status Required)
                    </button>
                @endif
            </div>
        </div>
    </div>


{{-- =============== 4. ACTIVATION RECEIPT DOWNLOAD PORTAL =============== --}}
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Activation Receipt</h3>
        <p class="card-subtitle">
            Download your official membership activation receipt.
        </p>
    </div>

    <div class="certificate-row">
        <div class="certificate-info">
            <div class="certificate-title-row">
                <span class="card-title">
                    Official Activation Receipt
                </span>

                @if($latestApprovedActivation)
                    <span class="status-badge-sm status-badge--active">
                        Available
                    </span>
                @else
                    <span class="status-badge-sm status-badge--inactive">
                        Unavailable
                    </span>
                @endif
            </div>

            <p class="certificate-desc">
                @if($latestApprovedActivation)
                    Your membership activation has been approved.
                    You can securely download your official receipt.
                @else
                    Your official activation receipt is not available yet.
                    A receipt will appear here once your membership activation
                    has been approved.
                @endif
            </p>
        </div>

        <div class="card-header-action">
            @if($latestApprovedActivation)

                <a
                    href="{{ route('activation.receipt.download', [
                        'activation' => $latestApprovedActivation->ActivationID
                    ]) }}"
                    class="btn-dark"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="btn-icon-dark"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"
                        />
                    </svg>

                    Download Receipt
                </a>

            @else

                <button
                    type="button"
                    disabled
                    class="btn-locked-lg"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="btn-icon-dark"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"
                        />
                    </svg>

                    Receipt Unavailable
                </button>

            @endif
        </div>
    </div>
</div>



</div>
