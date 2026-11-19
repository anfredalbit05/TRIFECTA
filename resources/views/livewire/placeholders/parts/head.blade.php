@php
    $titleWidth = $title ?? 220;
    $descWidth = $desc ?? 340;
    $withSelect = $select ?? false;
    $withAction = $action ?? false;
    $withRefresh = $refresh ?? false;
@endphp

<div style="display:flex; align-items:flex-start; justify-content:space-between; gap:16px; flex-wrap:wrap; margin-bottom:18px;">

    <div style="min-width:0; flex:1 1 240px;">
        <div class="skeleton" style="width:{{ $titleWidth }}px; max-width:100%; height:22px; margin-bottom:10px;"></div>
        <div class="skeleton" style="width:{{ $descWidth }}px; max-width:100%; height:12px;"></div>
    </div>

    @if ($withSelect || $withAction || $withRefresh)
        <div style="display:flex; align-items:center; gap:10px;">

            @if ($withSelect)
                <div class="skeleton" style="width:92px; height:36px; border-radius:10px;"></div>
            @endif

            @if ($withAction)
                <div class="skeleton" style="width:132px; height:38px; border-radius:10px;"></div>
            @endif

            @if ($withRefresh)
                <div class="skeleton" style="width:104px; height:38px; border-radius:10px;"></div>
            @endif

        </div>
    @endif

</div>
