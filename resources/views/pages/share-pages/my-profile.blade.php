@extends('layouts.member')

@section('title', 'Account Settings')

@section('content')

<div
    class="ws-page"
    x-data="{ tab: $persist('profile').as('my-profile-tab') }"
    @account-activated.window="$wire.$refresh()"
>

    <header class="ws-head">
        <div>
            <span class="ws-head-kicker">Personal · Account</span>
            <h1>Account Settings</h1>
            <p>Keep your identity, contact details and sign-in security current so your cover never lapses.</p>
        </div>

        <div class="ws-head-side">
            <div class="my-profile-tabs-activation">
                <livewire:member-pages.my-profile.activation-button />
            </div>
        </div>
    </header>

    <div class="ws-grid">

        <aside class="ws-rail">

            <div class="ws-rail-group">
                <div class="ws-rail-label">Identity</div>

                <a href="#" class="ws-rail-link" :class="tab === 'profile' ? 'is-active' : ''"
                   @click.prevent="tab = 'profile'">
                    <span class="ws-rail-icon">♙</span>
                    <span>Profile</span>
                </a>

                <a href="#" class="ws-rail-link" :class="tab === 'account' ? 'is-active' : ''"
                   @click.prevent="tab = 'account'">
                    <span class="ws-rail-icon">◉</span>
                    <span>Account</span>
                </a>
            </div>

            <div class="ws-rail-group">
                <div class="ws-rail-label">Access</div>

                <a href="#" class="ws-rail-link" :class="tab === 'security' ? 'is-active' : ''"
                   @click.prevent="tab = 'security'">
                    <span class="ws-rail-icon">🛡</span>
                    <span>Security</span>
                </a>

                @if(auth()->user()->RoleID == 8)
                    <a href="#" class="ws-rail-link" :class="tab === 'certificate' ? 'is-active' : ''"
                       @click.prevent="tab = 'certificate'">
                        <span class="ws-rail-icon">▣</span>
                        <span>Policy & Receipt</span>
                    </a>
                @endif
            </div>

        </aside>

        <section class="ws-panel">

            <div x-show="tab === 'profile'" x-cloak class="my-profile-main-card">
                <livewire:member-pages.my-profile.profile lazy />
            </div>

            <div x-show="tab === 'account'" x-cloak class="my-profile-main-card">
                <livewire:member-pages.my-profile.account lazy />
            </div>

            <div x-show="tab === 'security'" x-cloak class="my-profile-main-card">
                <livewire:member-pages.my-profile.security lazy />
            </div>

            <div x-show="tab === 'activity'" x-cloak class="my-profile-main-card">
                <livewire:member-pages.my-profile.activity lazy />
            </div>

            @if(auth()->user()->RoleID == 8)
                <div x-show="tab === 'certificate'" x-cloak class="my-profile-main-card">
                    <livewire:member-pages.my-profile.certificate lazy />
                </div>
            @endif

        </section>

    </div>

    <livewire:member-pages.my-profile.activation-modal />

</div>

{{-- Signature pad library for the profile tab's signature pad. --}}
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>

{{-- The profile tabs are lazy Livewire components, so their Alpine factories
     must be registered before each lazy component mounts. Load the modules
     here on the static page instead of inside the lazy components. --}}
@vite([
    'resources/js/livewire/member-pages/my-profile/profile.js',
    'resources/js/livewire/member-pages/my-profile/account.js',
    'resources/js/livewire/member-pages/my-profile/security.js',
])

@endsection
