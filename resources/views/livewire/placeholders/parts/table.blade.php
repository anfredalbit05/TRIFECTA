@php
    $columns = $cols ?? [1];
    $rowCount = $rows ?? 8;
    $grid = implode(' ', array_map(fn ($weight) => $weight.'fr', $columns));
@endphp

<div class="skeleton-table">

    <div class="skeleton-table-heading" style="grid-template-columns: {{ $grid }};">
        @foreach ($columns as $index => $weight)
            <div class="skeleton skeleton-line" style="width:{{ $index === 0 ? 30 : 70 }}%;"></div>
        @endforeach
    </div>

    @for ($row = 0; $row < $rowCount; $row++)
        <div class="skeleton-table-row" style="grid-template-columns: {{ $grid }}; border-top:1px solid var(--color-primary-tint);">
            @foreach ($columns as $index => $weight)
                <div class="skeleton skeleton-line" style="width:{{ $index === 0 ? 30 : ((($row + $index) % 3 === 0) ? 55 : 80) }}%;"></div>
            @endforeach
        </div>
    @endfor

</div>

@if ($pager ?? false)
    <div style="display:flex; align-items:center; justify-content:center; gap:12px; margin-top:14px;">
        <div class="skeleton" style="width:82px; height:32px; border-radius:8px;"></div>
        <div class="skeleton" style="width:112px; height:12px;"></div>
        <div class="skeleton" style="width:62px; height:32px; border-radius:8px;"></div>
    </div>
@endif
