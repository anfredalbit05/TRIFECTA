{{-- Loading skeleton for member-pages › genealogy › genealogy-tree — mirrors that tab's real layout. --}}
<div class="report-skeleton">

    @include('livewire.placeholders.parts.head', ['title' => 200, 'desc' => 320])
    @include('livewire.placeholders.parts.toolbar', ['search' => true, 'refresh' => true])
    @include('livewire.placeholders.parts.tree', ['nodes' => 6])

</div>
