{{-- Loading skeleton for admin-pages › transaction-list › disbursements — mirrors that tab's real layout. --}}
<div class="report-skeleton">

    @include('livewire.placeholders.parts.cards', ['count' => 4, 'min' => 160, 'icon' => false])
    @include('livewire.placeholders.parts.toolbar', ['search' => true, 'add' => true, 'refresh' => true])
    @include('livewire.placeholders.parts.table', ['cols' => [1, 2.2, 1.4, 1.2, 2, 1.4, 1.2], 'rows' => 8])

</div>
