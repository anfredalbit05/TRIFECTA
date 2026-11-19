{{-- Loading skeleton for member-pages › my-profile › account — mirrors that tab's real layout. --}}
<div class="report-skeleton">

    @include('livewire.placeholders.parts.head', ['title' => 220, 'desc' => 420])
    @include('livewire.placeholders.parts.form', ['fields' => 2, 'cols' => 2])
    @include('livewire.placeholders.parts.form', ['fields' => 4, 'cols' => 2])

</div>
