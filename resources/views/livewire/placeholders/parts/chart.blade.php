@php
    $chartHeight = $height ?? 260;
@endphp

<div class="skeleton-table" style="padding:18px; margin-bottom:16px;">

    <div class="skeleton skeleton-line" style="width:220px; height:14px; margin-bottom:18px;"></div>

    <div class="skeleton" style="width:100%; height:{{ $chartHeight }}px; border-radius:12px;"></div>

</div>
