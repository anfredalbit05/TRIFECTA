{{-- Loading skeleton for admin-pages › report-settings › general-settings — mirrors that tab's real layout. --}}
<div class="report-skeleton">

    @include('livewire.placeholders.parts.head', ['title' => 260, 'desc' => 460, 'action' => true])
    @include('livewire.placeholders.parts.toolbar', ['search' => true, 'refresh' => true])
    @include('livewire.placeholders.parts.table', ['cols' => [0.6, 3.4, 2.6, 1.2], 'rows' => 8, 'pager' => true])

</div>
