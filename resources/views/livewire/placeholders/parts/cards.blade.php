@php
    $cardCount = $count ?? 4;
    $minWidth = $min ?? 170;
    $withIcon = $icon ?? true;
@endphp

<div style="display:grid; grid-template-columns:repeat(auto-fit, minmax({{ $minWidth }}px, 1fr)); gap:14px; margin-bottom:20px;">

    @for ($i = 0; $i < $cardCount; $i++)
        <div class="skeleton-card">
            @if ($withIcon)
                <div class="skeleton skeleton-icon"></div>
            @endif

            <div class="skeleton skeleton-number" style="width:{{ $withIcon ? 58 : 46 }}%;"></div>
            <div class="skeleton skeleton-line" style="width:86%; margin-top:10px;"></div>
        </div>
    @endfor

</div>
