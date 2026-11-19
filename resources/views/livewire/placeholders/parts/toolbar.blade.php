@php
    $withSearch = $search ?? true;
    $withAdd = $add ?? false;
    $withCount = $count ?? false;
    $withRefresh = $refresh ?? true;
@endphp

<div style="display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap; margin-bottom:16px;">

    @if ($withSearch)
        <div class="skeleton" style="flex:1 1 260px; max-width:420px; height:40px; border-radius:10px;"></div>
    @else
        <div style="flex:1 1 0;"></div>
    @endif

    <div style="display:flex; align-items:center; gap:10px;">

        @if ($withCount)
            <div class="skeleton" style="width:96px; height:12px;"></div>
        @endif

        @if ($withAdd)
            <div class="skeleton" style="width:132px; height:40px; border-radius:10px;"></div>
        @endif

        @if ($withRefresh)
            <div class="skeleton" style="width:104px; height:40px; border-radius:10px;"></div>
        @endif

    </div>

</div>
