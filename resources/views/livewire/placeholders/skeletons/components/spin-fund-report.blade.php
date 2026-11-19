{{-- Loading skeleton for components › spin-fund-report — mirrors that tab's real layout. --}}
<div class="report-skeleton">

    @include('livewire.placeholders.parts.head', ['title' => 280, 'desc' => 300, 'action' => true])
    @include('livewire.placeholders.parts.toolbar', ['search' => true, 'refresh' => false])
    @include('livewire.placeholders.parts.table', ['cols' => [1.4, 1.4, 1.1, 1, 1, 1.1, 1.1, 1.2, 1], 'rows' => 8])

</div>
