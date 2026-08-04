@extends('layouts.member')

@section('title', 'Genealogy Tree')

@section('content')

<div class="genealogy-container" x-data="{ tab: 'invitee' }">

    <!-- HEADER -->
    <nav class="genealogy-nav">
        <div class="genealogy-nav-title">
            <h1>Genealogy</h1>
        </div>
    </nav>

    <!-- CONTENT -->
    <div class="genealogy-layout pt-4">

        <!-- MENU -->
        <div class="genealogy-nav-menu">

            <a href="#"
               @click.prevent="tab = 'invitee'"
               :class="tab === 'invitee' ? 'active' : ''">
                Invitee
            </a>

            <a href="#"
               @click.prevent="tab = 'board-member'"
               :class="tab === 'board-member' ? 'active' : ''">
                Board Member
            </a>

        </div>

        <!-- INVITEE -->
        <div x-show="tab === 'invitee'"
             x-cloak
             class="tab-card full-width-card">

            <div class="tree-wrapper">
                <livewire:member-pages.genealogy.genealogy-tree />
            </div>

        </div>

        <!-- BOARD MEMBER -->
        <div x-show="tab === 'board-member'"
             x-cloak
             class="tab-card">

            @include('livewire.member-pages.genealogy.board-members')

        </div>

    </div>

</div>

@endsection
