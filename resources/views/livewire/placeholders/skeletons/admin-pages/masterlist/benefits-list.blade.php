{{-- Loading skeleton for admin-pages › masterlist › benefits-list — mirrors that tab's real layout. --}}
<div class="report-skeleton">

    @include('livewire.placeholders.parts.head', ['title' => 180, 'desc' => 360, 'action' => true])
    @include('livewire.placeholders.parts.toolbar', ['search' => true, 'count' => true, 'refresh' => true])
    @include('livewire.placeholders.parts.table', ['cols' => [0.6, 2.2, 3, 1.4, 1.4, 1.2, 1.2], 'rows' => 8, 'pager' => true])

</div>
