{{-- Loading skeleton for admin-pages › post › overview — mirrors that tab's real layout. --}}
<div class="report-skeleton">

    @include('livewire.placeholders.parts.head', ['title' => 160, 'desc' => 320, 'refresh' => true])
    @include('livewire.placeholders.parts.cards', ['count' => 4, 'min' => 200, 'icon' => false])
    @include('livewire.placeholders.parts.lists', ['cols' => 2, 'rows' => 4, 'footer' => false])

</div>
