{{-- Loading skeleton for member-pages › genealogy › overview — mirrors that tab's real layout. --}}
<div class="report-skeleton">

    @include('livewire.placeholders.parts.head', ['title' => 160, 'desc' => 300])
    @include('livewire.placeholders.parts.toolbar', ['search' => true, 'refresh' => false])
    @include('livewire.placeholders.parts.cards', ['count' => 1, 'min' => 100, 'icon' => false])
    @include('livewire.placeholders.parts.table', ['cols' => [0.6, 2, 1.2, 1.4, 1.6, 1, 1.2], 'rows' => 6])

</div>
