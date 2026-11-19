{{-- Loading skeleton for admin-pages › masterlist › admin-users — mirrors that tab's real layout. --}}
<div class="report-skeleton">

    @include('livewire.placeholders.parts.head', ['title' => 200, 'desc' => 320, 'action' => true])
    @include('livewire.placeholders.parts.toolbar', ['search' => true, 'count' => true, 'refresh' => true])
    @include('livewire.placeholders.parts.table', ['cols' => [0.6, 1.8, 1.4, 1.4, 1.4, 1.2, 1.2, 1, 1, 1], 'rows' => 8, 'pager' => true])

</div>
