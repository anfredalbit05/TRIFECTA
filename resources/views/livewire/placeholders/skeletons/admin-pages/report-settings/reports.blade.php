{{-- Loading skeleton for admin-pages › report-settings › reports — mirrors that tab's real layout. --}}
<div class="report-skeleton">

    @include('livewire.placeholders.parts.head', ['title' => 180, 'desc' => 320])
    @include('livewire.placeholders.parts.toolbar', ['search' => true, 'refresh' => true])
    @include('livewire.placeholders.parts.table', ['cols' => [0.6, 1.6, 2, 1.4, 1.4, 1.2, 1.2, 1.2], 'rows' => 8, 'pager' => true])

</div>
