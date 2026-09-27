@extends('layouts.member')

@section('title', 'Satellite')

@section('content')

<div class="report-settings-container">

    <nav class="report-settings-header">
        <div class="report-settings-header-title">
            <h1>Satellite</h1>
        </div>
    </nav>

    <div class="report-settings-main-card">
        <livewire:member-pages.satellite.satellite-members-self />
    </div>

</div>

@endsection
