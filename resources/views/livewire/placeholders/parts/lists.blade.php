@php
    $columnCount = $cols ?? 3;
    $rowCount = $rows ?? 5;
    $withFooter = $footer ?? true;
@endphp

<div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(240px, 1fr)); gap:16px; margin-bottom:20px;">

    @for ($column = 0; $column < $columnCount; $column++)
        <div class="skeleton-table" style="padding:18px;">

            <div class="skeleton skeleton-line" style="width:62%; height:14px; margin-bottom:16px;"></div>

            @for ($row = 0; $row < $rowCount; $row++)
                <div style="display:flex; align-items:center; justify-content:space-between; gap:12px; padding:9px 0;">
                    <div class="skeleton skeleton-line" style="width:54%;"></div>
                    <div class="skeleton skeleton-line" style="width:22%;"></div>
                </div>
            @endfor

            @if ($withFooter)
                <div style="display:flex; align-items:center; justify-content:space-between; gap:12px; margin-top:14px; padding-top:12px; border-top:1px solid var(--color-primary-tint);">
                    <div class="skeleton skeleton-line" style="width:38%; height:13px;"></div>
                    <div class="skeleton skeleton-line" style="width:24%; height:13px;"></div>
                </div>
            @endif

        </div>
    @endfor

</div>
