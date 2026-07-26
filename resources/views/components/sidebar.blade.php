@php
    $isAdmin = auth()->check() && auth()->user()->RoleID == 1;
@endphp

<div x-data="{ open: false }" class="sidebar-wrapper">

    <!-- Mobile Header -->
    <header class="mobile-header">
        <button
            @click="open = !open"
            class="menu-button">

            <svg
                x-show="!open"
                xmlns="http://www.w3.org/2000/svg"
                class="menu-icon"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor">

                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M4 6h16M4 12h16M4 18h16" />
            </svg>

            <svg
                x-show="open"
                x-cloak
                xmlns="http://www.w3.org/2000/svg"
                class="menu-icon"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor">

                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M6 18L18 6M6 6l12 12" />
            </svg>

        </button>

        <img
            src="{{ asset('images/dayongan-logo.png') }}"
            class="mobile-logo">
    </header>


    <!-- Overlay -->
    <div
        x-show="open"
        x-transition.opacity
        x-cloak
        @click="open = false"
        class="sidebar-overlay">
    </div>


    <!-- Sidebar -->
    <aside
        :class="open ? 'sidebar-open' : 'sidebar-close'"
        class="sidebar">


        <!-- Fixed Sidebar Logo -->
        <a href="{{ route('home') }}" class="sidebar-logo">
            <img
                src="{{ asset('images/dayongan-logo.png') }}"
                class="logo-image">
        </a>


        <!-- Scroll Area -->
        <div class="sidebar-content">

            <div class="profile-card sidebar-scroll">


            <!-- Profile -->
            <div class="profile-section">

                <div class="profile-image-box">
                    <img
                        src="{{ asset('images/user.png') }}"
                        class="profile-image">
                </div>

                <h2 class="profile-name">
                    {{ Auth::user()->firstname ?? 'Member' }}
                    {{ Auth::user()->lastname ?? '' }}
                </h2>

                <p class="profile-status">
                    {{ Auth::user()->status ?? 'Inactive' }} Member
                </p>


            </div>


                <!-- Menu -->
                <div class="menu-list">

                <a
                    href="{{ route('dashboard') }}"
                    class="menu-item {{ request()->routeIs('dashboard') ? 'active-menu' : '' }}">
                    Dashboard
                </a>


                @if($isAdmin)

                <a
                    href="{{ route('masterlist') }}"
                    class="menu-item {{ request()->routeIs('masterlist') ? 'active-menu' : '' }}">
                    Masterlist
                </a>

                <a
                    href="{{ route('post') }}"
                    class="menu-item {{ request()->routeIs('post') ? 'active-menu' : '' }}">
                    Post
                </a>

                <a
                    href="{{ route('report-settings') }}"
                    class="menu-item {{ request()->routeIs('report-settings') ? 'active-menu' : '' }}">
                    Report & Settings
                </a>
                   @endif

                <a
                    {{-- href="{{ route('my-profile') }}" --}}
                    class="menu-item {{ request()->routeIs('my-profile') ? 'active-menu' : '' }}">
                    My Profile
                </a>

                </div>


            </div>

        </div>


        <!-- Logout -->
        <div class="logout-area">

            <livewire:logout-button />

        </div>


    </aside>

</div>
