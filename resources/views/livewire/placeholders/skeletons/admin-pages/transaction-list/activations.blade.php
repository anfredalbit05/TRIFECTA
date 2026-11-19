{{-- Loading skeleton for admin-pages › transaction-list › activations — mirrors that tab's real layout. --}}
<div class="report-skeleton">

    @include('livewire.placeholders.parts.head', ['title' => 260, 'desc' => 300])
    @include('livewire.placeholders.parts.toolbar', ['search' => true, 'refresh' => true])
    @include('livewire.placeholders.parts.table', ['cols' => [0.6, 1.8, 1.1, 1.4, 1.1, 1.1, 1.2, 1, 1], 'rows' => 8, 'pager' => true])

</div>
