{{-- Loading skeleton for member-pages › my-profile › profile — mirrors that tab's real layout. --}}
<div class="report-skeleton">

    @include('livewire.placeholders.parts.head', ['title' => 320, 'desc' => 420])
    @include('livewire.placeholders.parts.form', ['fields' => 8, 'cols' => 2])
    @include('livewire.placeholders.parts.table', ['cols' => [2, 1.4, 1.4], 'rows' => 3])
    @include('livewire.placeholders.parts.form', ['fields' => 2, 'cols' => 1])

</div>
