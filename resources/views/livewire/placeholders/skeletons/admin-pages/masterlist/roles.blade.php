{{-- Loading skeleton for admin-pages › masterlist › roles — mirrors that tab's real layout. --}}
<div class="report-skeleton">

    @include('livewire.placeholders.parts.head', ['title' => 140, 'desc' => 320, 'action' => true])
    @include('livewire.placeholders.parts.toolbar', ['search' => true, 'refresh' => true])
    @include('livewire.placeholders.parts.table', ['cols' => [0.6, 2.4, 3.2, 1.2, 1.2], 'rows' => 6, 'pager' => true])

</div>
