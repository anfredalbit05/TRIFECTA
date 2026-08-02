<div class="w-full space-y-6">

    <!-- Header -->
    <div>
        <h2 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900">
            Policy & Certificates
        </h2>
        <p class="mt-1 text-xs sm:text-sm text-slate-500">
            View your policy guidelines, terms, and download your official documents.
        </p>
    </div>

    <!-- Policy Information Card -->
    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 bg-slate-50 px-4 sm:px-6 py-4 sm:py-5 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <h3 class="font-semibold text-sm sm:text-base text-slate-900">
                    Membership & Certificate Policy
                </h3>
                <p class="mt-0.5 text-xs sm:text-sm text-slate-500">
                    Please review the guidelines regarding certificate issuance and account status.
                </p>
            </div>
            <div class="w-full sm:w-auto">
                @if(auth()->user()->status === 'Active')
                    <a href="{{ route('policy.download') }}"
                       target="_blank"
                       class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-xl bg-white px-4 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-50 border border-slate-200 shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Download Policy File
                    </a>
                @else
                    <button type="button"
                            disabled
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-xl bg-slate-50 px-4 py-2 text-xs font-semibold text-slate-400 cursor-not-allowed border border-slate-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        Policy Locked (Active Status Required)
                    </button>
                @endif
            </div>
        </div>

        <div class="p-4 sm:p-6 space-y-4 text-xs sm:text-sm text-slate-600 leading-relaxed">
            <div class="flex items-start gap-3">
                <div class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-slate-100 text-slate-700 font-semibold text-xs">
                    1
                </div>
                <p>
                    <strong class="text-slate-900">Active Status Requirement:</strong> Official certificates (such as the Certificate of Membership or Recognition) are exclusively available for download to users who maintain an <span class="font-semibold text-emerald-600">Active</span> account status.
                </p>
            </div>

            <div class="flex items-start gap-3">
                <div class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-slate-100 text-slate-700 font-semibold text-xs">
                    2
                </div>
                <p>
                    <strong class="text-slate-900">Validity & Compliance:</strong> Downloaded certificates contain secure reference identifiers linked directly to your account profile. Tampering with or altering digital certificate copies violates system policies.
                </p>
            </div>

            <div class="flex items-start gap-3">
                <div class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-slate-100 text-slate-700 font-semibold text-xs">
                    3
                </div>
                <p>
                    <strong class="text-slate-900">Account Suspension:</strong> If an account status changes to <span class="font-semibold text-amber-600">Inactive</span> or <span class="font-semibold text-red-600">Suspended</span>, download privileges are automatically revoked until compliance or activation requirements are fulfilled.
                </p>
            </div>
        </div>
    </div>

    <!-- Certificate Claim / Download Portal Card -->
    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 bg-slate-50 px-4 sm:px-6 py-4 sm:py-5">
            <h3 class="font-semibold text-sm sm:text-base text-slate-900">
                Certificate Download Portal
            </h3>
            <p class="mt-0.5 text-xs sm:text-sm text-slate-500">
                Claim and download your official document copy here.
            </p>
        </div>

        <div class="p-4 sm:p-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 sm:gap-6">
            <div class="space-y-1">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="font-semibold text-sm sm:text-base text-slate-900">Official Status Certificate</span>
                    <span @class([
                        'inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold',
                        'bg-emerald-100 text-emerald-700' => auth()->user()->status === 'Active',
                        'bg-amber-100 text-amber-700' => auth()->user()->status === 'Inactive',
                        'bg-red-100 text-red-700' => auth()->user()->status === 'Suspended',
                    ])>
                        {{ auth()->user()->status }}
                    </span>
                </div>
                <p class="text-xs sm:text-sm text-slate-500">
                    @if(auth()->user()->status === 'Active')
                        Your account qualifies. You can securely claim and download your document.
                    @else
                        Your account status must be Active to unlock this certificate file.
                    @endif
                </p>
            </div>

            <div class="w-full sm:w-auto">
                @if(auth()->user()->status === 'Active')
                    <a href="{{ route('certificate.download') }}"
                       class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-xl bg-slate-900 px-5 sm:px-6 py-2.5 sm:py-3 text-xs sm:text-sm font-semibold text-white hover:bg-slate-800 shadow-sm transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        Download Certificate File
                    </a>
                @else
                    <button type="button"
                            disabled
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-xl bg-slate-100 px-5 sm:px-6 py-2.5 sm:py-3 text-xs sm:text-sm font-semibold text-slate-400 cursor-not-allowed border border-slate-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        Locked (Active Status Required)
                    </button>
                @endif
            </div>
        </div>
    </div>

</div>
