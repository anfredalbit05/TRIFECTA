{{-- Loading skeleton for admin-pages › transaction-list › overview — mirrors that tab's real layout. --}}
<div class="report-skeleton">

    @include('livewire.placeholders.parts.head', ['title' => 240, 'desc' => 300])
    @include('livewire.placeholders.parts.cards', ['count' => 4, 'min' => 200, 'icon' => false])
    @include('livewire.placeholders.parts.table', ['cols' => [1, 2, 1.4], 'rows' => 6])

</div>
