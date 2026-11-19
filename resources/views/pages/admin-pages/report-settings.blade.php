@extends('layouts.member')

@section('title', 'Operations Desk')

@push('styles')
    @vite('resources/css/admin/print/report-settings.css')
@endpush

@section('content')

<div class="ws-page"
     x-data="{
        tab: $persist('overview').as('report-settings-tab'),

        switchTab(name) {
            this.tab = name;

            this.$nextTick(() => {
                if (window.Livewire && typeof Livewire.rescan === 'function') {
                    Livewire.rescan();
                }
            });
        }
     }">

    <header class="ws-head">
        <div>
            <span class="ws-head-kicker">Reports · Settings</span>
            <h1>Operations Desk</h1>
            <p>Read the numbers, tune the rules of the programme, and keep every configuration decision in one place.</p>
        </div>

        <div class="ws-head-side">
            <span class="ws-head-pill">Financial year · <strong>{{ date('Y') }}</strong></span>
        </div>
    </header>

    <div class="ws-grid">

        <aside class="ws-rail">

            <div class="ws-rail-group">
                <div class="ws-rail-label">Insights</div>

                <a href="#" class="ws-rail-link" :class="tab === 'overview' ? 'is-active' : ''"
                   @click.prevent="switchTab('overview')">
                    <span class="ws-rail-icon">⌂</span>
                    <span>Overview</span>
                </a>

                <a href="#" class="ws-rail-link" :class="tab === 'users' ? 'is-active' : ''"
                   @click.prevent="switchTab('users')">
                    <span class="ws-rail-icon">♟</span>
                    <span>Users Reports</span>
                </a>

                <a href="#" class="ws-rail-link" :class="tab === 'reports' ? 'is-active' : ''"
                   @click.prevent="switchTab('reports')">
                    <span class="ws-rail-icon">▤</span>
                    <span>Ledgers</span>
                </a>
            </div>

            @if(auth()->user()->RoleID == 1)
                <div class="ws-rail-group">
                    <div class="ws-rail-label">Configuration</div>

                    <a href="#" class="ws-rail-link" :class="tab === 'general' ? 'is-active' : ''"
                       @click.prevent="switchTab('general')">
                        <span class="ws-rail-icon">⚙</span>
                        <span>General</span>
                    </a>

                    <a href="#" class="ws-rail-link" :class="tab === 'variants' ? 'is-active' : ''"
                       @click.prevent="switchTab('variants')">
                        <span class="ws-rail-icon">▦</span>
                        <span>Variants</span>
                    </a>
                </div>
            @endif

            @if(in_array(auth()->user()->RoleID, [1,2,5,6,7]))
                <div class="ws-rail-group">
                    <div class="ws-rail-label">Growth</div>

                    <a href="#" class="ws-rail-link" :class="tab === 'pangkabuhayan' ? 'is-active' : ''"
                       @click.prevent="switchTab('pangkabuhayan')">
                        <span class="ws-rail-icon">◎</span>
                        <span>Pangkabuhayan</span>
                    </a>

                    <a href="#" class="ws-rail-link" :class="tab === 'sales' ? 'is-active' : ''"
                       @click.prevent="switchTab('sales')">
                        <span class="ws-rail-icon">◔</span>
                        <span>Sales Consultant Report</span>
                    </a>
                </div>
            @endif

        </aside>

        <section class="ws-panel">

            {{-- OVERVIEW --}}
            <template x-if="tab === 'overview'">
                <div class="report-settings-main-card">
                    <livewire:admin-pages.report-settings.overview lazy />
                </div>
            </template>

            {{-- USERS REPORTS --}}
            <template x-if="tab === 'users'">
                <div class="report-settings-main-card">
                    <livewire:admin-pages.report-settings.user-accounts lazy />
                </div>
            </template>

            {{-- LEDGERS --}}
            <template x-if="tab === 'reports'">
                <div class="report-settings-main-card">
                    <livewire:admin-pages.report-settings.reports lazy />
                </div>
            </template>

            <template x-if="tab === 'sales'">
                <div class="report-settings-main-card">
                    <livewire:admin-pages.report-settings.sales-consultant-report lazy />
                </div>
            </template>

            @if(in_array(auth()->user()->RoleID, [1,2,5,6,7]))
                <template x-if="tab === 'pangkabuhayan'">
                    <div class="report-settings-main-card">
                        <livewire:components.spin-fund-report lazy />
                    </div>
                </template>
            @endif

            @if(auth()->user()->RoleID == 1)
                <template x-if="tab === 'variants'">
                    <div class="report-settings-main-card">
                        <livewire:admin-pages.report-settings.variants lazy />
                    </div>
                </template>

                <template x-if="tab === 'general'">
                    <div class="report-settings-main-card">
                        <livewire:admin-pages.report-settings.general-settings lazy />
                    </div>
                </template>
            @endif

        </section>

    </div>

</div>

{{-- Signature pad library for the report-settings "Others" (signature) value
     type — loaded once here on the static page (NOT inside the Livewire
     component, where modal re-renders would re-process it and cause lag). --}}
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>

{{-- The overview tab is a lazy Livewire component, so its Alpine factory
     must be registered before Alpine starts. Load the module here on the
     static page instead of inside the lazy component. --}}
@vite([
    'resources/js/pages/admin-pages/report-settings.js',
    'resources/js/livewire/admin-pages/report-settings/overview.js',
    'resources/js/livewire/components/spin-fund-report.js',
])

@endsection
