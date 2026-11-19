@php
    $fieldCount = $fields ?? 6;
    $columnCount = $cols ?? 2;
@endphp

<div class="skeleton-table" style="padding:18px; margin-bottom:16px;">

    <div class="skeleton skeleton-line" style="width:180px; height:14px; margin-bottom:10px;"></div>
    <div class="skeleton skeleton-line" style="width:260px; height:11px; margin-bottom:20px;"></div>

    <div style="display:grid; grid-template-columns:repeat({{ $columnCount }}, minmax(0, 1fr)); gap:14px 18px;">

        @for ($i = 0; $i < $fieldCount; $i++)
            <div>
                <div class="skeleton skeleton-line" style="width:90px; height:10px; margin-bottom:9px;"></div>
                <div class="skeleton" style="width:100%; height:38px; border-radius:8px;"></div>
            </div>
        @endfor

    </div>

</div>
