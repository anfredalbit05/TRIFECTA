{{-- Loading skeleton for admin-pages › report-settings › sales-consultant-report — mirrors that tab's real layout. --}}
<div class="report-skeleton">

    @include('livewire.placeholders.parts.head', ['title' => 300, 'desc' => 320, 'select' => true])
    @include('livewire.placeholders.parts.toolbar', ['search' => true, 'refresh' => true])
    @include('livewire.placeholders.parts.table', ['cols' => [1.4, 1.1, 2, 1, 0.9, 0.9, 0.9, 0.9, 0.9], 'rows' => 8, 'pager' => true])

</div>
