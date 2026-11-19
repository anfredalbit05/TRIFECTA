{{-- Loading skeleton for share-pages › dashboard › news-updates — mirrors that tab's real layout. --}}
<div class="report-skeleton">

    @include('livewire.placeholders.parts.head', ['title' => 200, 'desc' => 300, 'refresh' => true])
    @include('livewire.placeholders.parts.lists', ['cols' => 1, 'rows' => 4, 'footer' => false])

</div>
