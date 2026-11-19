{{-- Loading skeleton for admin-pages › post › create-news-updates — mirrors that tab's real layout. --}}
<div class="report-skeleton">

    @include('livewire.placeholders.parts.head', ['title' => 240, 'desc' => 320, 'action' => true])
    @include('livewire.placeholders.parts.toolbar', ['search' => true, 'count' => true, 'refresh' => true])
    @include('livewire.placeholders.parts.table', ['cols' => [0.6, 2.6, 2.4, 1.2, 1.2, 1.4, 1.2], 'rows' => 8, 'pager' => true])

</div>
