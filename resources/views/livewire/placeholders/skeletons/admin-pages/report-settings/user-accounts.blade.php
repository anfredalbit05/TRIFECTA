{{-- Loading skeleton for admin-pages › report-settings › user-accounts — mirrors that tab's real layout. --}}
<div class="report-skeleton">

    @include('livewire.placeholders.parts.head', ['title' => 340, 'desc' => 420, 'action' => true])
    @include('livewire.placeholders.parts.toolbar', ['search' => false, 'count' => true, 'refresh' => true])
    @include('livewire.placeholders.parts.table', ['cols' => [0.6, 2.4, 1.2, 1.4, 1.4, 1.4, 1.4, 1.2], 'rows' => 8, 'pager' => true])

</div>
