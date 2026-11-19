{{-- Loading skeleton for member-pages › my-profile › activity — mirrors that tab's real layout. --}}
<div class="report-skeleton">

    @include('livewire.placeholders.parts.head', ['title' => 220, 'desc' => 420])
    @include('livewire.placeholders.parts.form', ['fields' => 1, 'cols' => 1])
    @include('livewire.placeholders.parts.lists', ['cols' => 1, 'rows' => 4, 'footer' => false])

</div>
