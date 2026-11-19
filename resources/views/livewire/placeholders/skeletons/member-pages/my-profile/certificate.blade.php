{{-- Loading skeleton for member-pages › my-profile › certificate — mirrors that tab's real layout. --}}
<div class="report-skeleton">

    @include('livewire.placeholders.parts.head', ['title' => 380, 'desc' => 460])
    @include('livewire.placeholders.parts.form', ['fields' => 1, 'cols' => 1])
    @include('livewire.placeholders.parts.cards', ['count' => 2, 'min' => 260, 'icon' => false])

</div>
