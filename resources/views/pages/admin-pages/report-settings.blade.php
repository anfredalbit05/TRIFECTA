@extends('layouts.member')

@section('title', 'Report & Settings')

@section('content')

<div class="report-settings-container"
     x-data="{ tab: $persist('overview').as('report-settings-tab') }">

    <nav class="report-settings-header">
        <div class="report-settings-header-title">
            <h1>Report & Settings</h1>
        </div>
    </nav>

    <div class="report-settings-layout">

        <div class="report-settings-desktop-nav">

            <a href="#"
               @click.prevent="tab='overview'"
               :class="{ active: tab==='overview' }">
                <span class="report-settings-tab-icon">⌂</span>
                <span>Overview</span>
            </a>

            @if(auth()->user()->RoleID == 1)
                <a href="#"
                   @click.prevent="tab='general'"
                   :class="{ active: tab==='general' }">
                    <span class="report-settings-tab-icon">⚙</span>
                    <span>General</span>
                </a>
            @endif

            @if(auth()->user()->RoleID == 1)
                <a href="#"
                   @click.prevent="tab='variants'"
                   :class="{ active: tab==='variants' }">
                    <span class="report-settings-tab-icon">▦</span>
                    <span>Variants</span>
                </a>
            @endif

            <a href="#"
               @click.prevent="tab='users'"
               :class="{ active: tab==='users' }">
                <span class="report-settings-tab-icon">♟</span>
                <span>Users Reports</span>
            </a>

            <a href="#"
               @click.prevent="tab='reports'"
               :class="{ active: tab==='reports' }">
                <span class="report-settings-tab-icon">▤</span>
                <span>Ledgers</span>
            </a>

            @if(in_array(auth()->user()->RoleID, [1,2,5,6,7]))
                <a href="#"
                   @click.prevent="tab='pangkabuhayan'"
                   :class="{ active: tab==='pangkabuhayan' }">
                    <span class="report-settings-tab-icon">🎡</span>
                    <span>Pangkabuhayan</span>
                </a>
            @endif

            @if(in_array(auth()->user()->RoleID, [1,2,]))
                <a href="#"
                   @click.prevent="tab='sales'"
                   :class="{ active: tab==='sales' }">
                    <span class="report-settings-tab-icon">▤</span>
                    <span>Sales Consultant Report</span>
                </a>
            @endif

        </div>

        <div x-show="tab==='overview'" x-cloak class="report-settings-main-card">
            <livewire:admin-pages.report-settings.overview />
        </div>



        <div x-show="tab==='users'" x-cloak class="report-settings-main-card">
            <livewire:admin-pages.report-settings.user-accounts />
        </div>

        <div x-show="tab==='reports'" x-cloak class="report-settings-main-card">
            <livewire:admin-pages.report-settings.reports />
        </div>


        <div x-show="tab==='sales'" x-cloak class="report-settings-main-card">
            <livewire:admin-pages.report-settings.sales-consultant-report />
        </div>


        @if(in_array(auth()->user()->RoleID, [1,2,5,6,7]))
            <div x-show="tab==='pangkabuhayan'" x-cloak class="report-settings-main-card">
                <livewire:components.spin-fund-report />
            </div>
        @endif

        @if(auth()->user()->RoleID == 1)
            <div x-show="tab==='variants'" x-cloak class="report-settings-main-card">
                <livewire:admin-pages.report-settings.variants />
            </div>
        @endif

        @if(auth()->user()->RoleID == 1)
            <div x-show="tab==='general'" x-cloak class="report-settings-main-card">
                <livewire:admin-pages.report-settings.general-settings />
            </div>
        @endif





    </div>

</div>

{{-- Signature pad library for the report-settings "Others" (signature) value
     type — loaded once here on the static page (NOT inside the Livewire
     component, where modal re-renders would re-process it and cause lag). --}}
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
    <script>
        function signaturePad() {
            return {
                pad: null,

                initPad() {
                    this.$nextTick(() => {
                        const canvas = this.$refs.pad;

                        canvas.width = canvas.offsetWidth || 520;
                        canvas.height = 140;

                        this.pad = new SignaturePad(canvas, {
                            backgroundColor: 'rgb(255, 255, 255)', // needed so the export isn't transparent
                            penColor: 'rgb(15, 23, 42)',
                        });
                    });
                },

                clear() {
                    this.pad?.clear();
                },

                save($wire) {
                    if (!this.pad || this.pad.isEmpty()) {
                        Swal.fire({
                            title: 'No Signature',
                            text: 'Please draw a signature first.',
                            icon: 'warning',
                            confirmButtonColor: '#0B2A5B',
                        });

                        return;
                    }

                    // raw base64 payload (same shape the file upload sends)
                    $wire.call('saveSignatureSetting', this.pad.toDataURL('image/png').split(',')[1]);
                },
            };
        }
    </script>

@endsection
