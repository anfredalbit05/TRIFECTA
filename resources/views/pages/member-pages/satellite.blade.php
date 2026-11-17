@extends('layouts.member')

@section('title', 'Satellite Hub')

@section('content')

<div class="ws-page">

    <header class="ws-head">
        <div>
            <span class="ws-head-kicker">Community · Satellite</span>
            <h1>Satellite Hub</h1>
            <p>Track the members who joined under your satellite code and the activations waiting on them.</p>
        </div>

        <div class="ws-head-side">
            <span class="ws-head-pill">Satellite programme</span>
        </div>
    </header>

    <section class="ws-panel report-settings-main-card">
        <livewire:member-pages.satellite.satellite-members-self />
    </section>

</div>

@endsection
