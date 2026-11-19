{{-- Loading skeleton for admin-pages › dashboard › overview-analytics — mirrors that tab's real layout. --}}
<div class="report-skeleton">

    @include('livewire.placeholders.parts.head', ['title' => 260, 'desc' => 360, 'select' => true, 'refresh' => true])
    @include('livewire.placeholders.parts.cards', ['count' => 4, 'min' => 170])
    @include('livewire.placeholders.parts.chart', ['height' => 220])
    @include('livewire.placeholders.parts.lists', ['cols' => 2, 'rows' => 5, 'footer' => false])

</div>
