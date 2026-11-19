{{-- Loading skeleton for admin-pages › report-settings › overview — mirrors that tab's real layout. --}}
<div class="report-skeleton">

    @include('livewire.placeholders.parts.head', ['title' => 320, 'desc' => 360, 'select' => true, 'refresh' => true])
    @include('livewire.placeholders.parts.cards', ['count' => 5, 'min' => 150])
    @include('livewire.placeholders.parts.lists', ['cols' => 3, 'rows' => 6])
    @include('livewire.placeholders.parts.chart', ['height' => 260])

</div>
