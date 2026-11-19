@php
    $nodeCount = $nodes ?? 6;
@endphp

<div class="skeleton-table" style="padding:22px;">

    <div class="skeleton skeleton-line" style="width:200px; height:14px; margin-bottom:20px;"></div>

    <div style="display:flex; flex-direction:column; align-items:center; gap:18px;">

        <div class="skeleton" style="width:210px; height:66px; border-radius:14px;"></div>

        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(168px, 1fr)); gap:14px; width:100%;">

            @for ($i = 0; $i < $nodeCount; $i++)
                <div class="skeleton" style="width:100%; height:58px; border-radius:12px;"></div>
            @endfor

        </div>

    </div>

</div>
