{{-- Loading skeleton for admin-pages › masterlist › dashboard — mirrors that tab's real layout. --}}
<div class="report-skeleton">

    @include('livewire.placeholders.parts.head', ['title' => 260, 'desc' => 420, 'refresh' => true])
    @include('livewire.placeholders.parts.cards', ['count' => 4, 'min' => 170])
    @include('livewire.placeholders.parts.lists', ['cols' => 2, 'rows' => 5, 'footer' => false])
    @include('livewire.placeholders.parts.table', ['cols' => [0.6, 2, 1.4, 1, 1.4, 1.6, 1.4], 'rows' => 6])

</div>
