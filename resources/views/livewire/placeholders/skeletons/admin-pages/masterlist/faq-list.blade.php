{{-- Loading skeleton for admin-pages › masterlist › faq-list — mirrors that tab's real layout. --}}
<div class="report-skeleton">

    @include('livewire.placeholders.parts.head', ['title' => 140, 'desc' => 320, 'action' => true])
    @include('livewire.placeholders.parts.toolbar', ['search' => true, 'count' => true, 'refresh' => true])
    @include('livewire.placeholders.parts.table', ['cols' => [0.6, 2.6, 3.6, 1.1, 1.1], 'rows' => 8, 'pager' => true])

</div>
