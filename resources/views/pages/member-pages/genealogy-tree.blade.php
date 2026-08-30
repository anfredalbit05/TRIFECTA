@extends('layouts.member')

@section('title', 'Genealogy')

@section('content')

<div
    class="genealogy-container"
    x-data="{
        tab: $persist('invitee').as('genealogy-tree-tab')
    }"
>


<!-- =========================================
     GENEALOGY NAVIGATION
========================================== -->
<nav class="genealogy-nav">

    <div class="genealogy-nav-title">
        <h1>Genealogy</h1>
    </div>

</nav>


<!-- =========================================
     PAGE CONTENT
========================================== -->
<div class="genealogy-layout">


    <!-- =========================================
         TAB NAVIGATION
    ========================================== -->
    <div class="genealogy-nav-menu">

        <button
            type="button"
            @click="tab = 'invitee'"
            :class="{ 'active': tab === 'invitee' }"
        >
            Invitee
        </button>


        <button
            type="button"
            @click="tab = 'board-member'"
            :class="{ 'active': tab === 'board-member' }"
        >
            Board Member
        </button>

    </div>


    <!-- =========================================
         INVITEE GENEALOGY
         ONLY THIS AREA SCROLLS
    ========================================== -->
    <div
        x-show="tab === 'invitee'"
        x-cloak
        x-transition.opacity.duration.200ms
        class="genealogy-tab-content genealogy-scroll-area"
    >

        <livewire:member-pages.genealogy.genealogy-tree />

    </div>


    <!-- =========================================
         BOARD MEMBERS
    ========================================== -->
    <div
        x-show="tab === 'board-member'"
        x-cloak
        x-transition.opacity.duration.200ms
        class="genealogy-tab-content genealogy-scroll-area"
    >

        @include('livewire.member-pages.genealogy.board-members')

    </div>

</div>


</div>

@endsection
