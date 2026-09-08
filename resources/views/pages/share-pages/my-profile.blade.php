@extends('layouts.member')

@section('title', 'My Profile')

@section('content')

<div
    class="my-profile-container"
    x-data="{ tab: $persist('profile').as('my-profile-tab') }"
    @account-activated.window="$wire.$refresh()"
>

    <!-- =========================================
         MY PROFILE HEADER
    ========================================== -->

    <nav class="my-profile-header">

        <div class="my-profile-header-title">
            <h1>My Profile</h1>
        </div>

    </nav>


    <!-- =========================================
         MAIN CONTENT
    ========================================== -->

    <div class="my-profile-layout">

        <!-- =====================================
             TABS BAR
        ====================================== -->

        <div class="my-profile-nav-menu">

            <!-- TAB LINKS -->
            <div class="my-profile-tabs-links">

                <!-- PROFILE -->
                <a
                    href="#"
                    @click.prevent="tab = 'profile'"
                    :class="tab === 'profile' ? 'active' : ''"
                >
                    <span class="my-profile-tab-icon">
                        ♙
                    </span>

                    <span class="my-profile-tab-label">
                        Profile
                    </span>
                </a>


                <!-- ACCOUNT -->
                <a
                    href="#"
                    @click.prevent="tab = 'account'"
                    :class="tab === 'account' ? 'active' : ''"
                >
                    <span class="my-profile-tab-icon">
                        ◉
                    </span>

                    <span class="my-profile-tab-label">
                        Account
                    </span>
                </a>


                <!-- SECURITY -->
                <a
                    href="#"
                    @click.prevent="tab = 'security'"
                    :class="tab === 'security' ? 'active' : ''"
                >
                    <span class="my-profile-tab-icon">
                        🛡
                    </span>

                    <span class="my-profile-tab-label">
                        Security
                    </span>
                </a>


                <!-- ACTIVITY -->
                <a
                    href="#"
                    @click.prevent="tab = 'activity'"
                    :class="tab === 'activity' ? 'active' : ''"
                >
                    <span class="my-profile-tab-icon">
                        ◷
                    </span>

                    <span class="my-profile-tab-label">
                        Activity
                    </span>
                </a>


                <!-- CERTIFICATE -->
                <a
                    href="#"
                    @click.prevent="tab = 'certificate'"
                    :class="tab === 'certificate' ? 'active' : ''"
                >
                    <span class="my-profile-tab-icon">
                        ▣
                    </span>

                    <span class="my-profile-tab-label">
                        Certificate
                    </span>
                </a>

            </div>


            <!-- ACTIVATION BUTTON -->
            <div class="my-profile-tabs-activation">
                <livewire:member-pages.my-profile.activation-button />
            </div>

        </div>


        <!-- =====================================
             TAB CONTENT
        ====================================== -->

        <!-- PROFILE -->
        <div
            x-show="tab === 'profile'"
            x-cloak
            class="my-profile-main-card"
        >
            <livewire:member-pages.my-profile.profile />
        </div>


        <!-- ACCOUNT -->
        <div
            x-show="tab === 'account'"
            x-cloak
            class="my-profile-main-card"
        >
            <livewire:member-pages.my-profile.account />
        </div>


        <!-- SECURITY -->
        <div
            x-show="tab === 'security'"
            x-cloak
            class="my-profile-main-card"
        >
            <livewire:member-pages.my-profile.security />
        </div>


        <!-- ACTIVITY -->
        <div
            x-show="tab === 'activity'"
            x-cloak
            class="my-profile-main-card"
        >
            <livewire:member-pages.my-profile.activity />
        </div>


        <!-- CERTIFICATE -->
        <div
            x-show="tab === 'certificate'"
            x-cloak
            class="my-profile-main-card"
        >
            <livewire:member-pages.my-profile.certificate />
        </div>

    </div>


    <!-- =========================================
         MOBILE BOTTOM NAVIGATION
    ========================================== -->

    <template x-teleport="body">

        <nav class="my-profile-mobile-nav">

            <!-- PROFILE -->
            <a
                href="#"
                @click.prevent="tab = 'profile'"
                :class="tab === 'profile' ? 'active' : ''"
                aria-label="Profile"
            >
                <span class="my-profile-mobile-icon">
                    ♙
                </span>

                <span class="my-profile-mobile-label">
                    Profile
                </span>
            </a>


            <!-- ACCOUNT -->
            <a
                href="#"
                @click.prevent="tab = 'account'"
                :class="tab === 'account' ? 'active' : ''"
                aria-label="Account"
            >
                <span class="my-profile-mobile-icon">
                    ◉
                </span>

                <span class="my-profile-mobile-label">
                    Account
                </span>
            </a>


            <!-- SECURITY -->
            <a
                href="#"
                @click.prevent="tab = 'security'"
                :class="tab === 'security' ? 'active' : ''"
                aria-label="Security"
            >
                <span class="my-profile-mobile-icon">
                    🛡
                </span>

                <span class="my-profile-mobile-label">
                    Security
                </span>
            </a>


            <!-- ACTIVITY -->
            <a
                href="#"
                @click.prevent="tab = 'activity'"
                :class="tab === 'activity' ? 'active' : ''"
                aria-label="Activity"
            >
                <span class="my-profile-mobile-icon">
                    ◷
                </span>

                <span class="my-profile-mobile-label">
                    Activity
                </span>
            </a>


            <!-- CERTIFICATE -->
            <a
                href="#"
                @click.prevent="tab = 'certificate'"
                :class="tab === 'certificate' ? 'active' : ''"
                aria-label="Certificate"
            >
                <span class="my-profile-mobile-icon">
                    ▣
                </span>

                <span class="my-profile-mobile-label">
                    Certificate
                </span>
            </a>

        </nav>

    </template>


    <!-- =========================================
         ACTIVATION MODAL
    ========================================== -->

    <livewire:member-pages.my-profile.activation-modal />

</div>

@endsection
