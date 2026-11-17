
<div class="report-skeleton">

    <div class="skeleton-header">
        <div class="skeleton skeleton-title"></div>
        <div class="skeleton skeleton-description"></div>
    </div>

    <div class="skeleton-cards">
        @for($i = 0; $i < 4; $i++)
            <div class="skeleton-card">
                <div class="skeleton skeleton-icon"></div>
                <div class="skeleton skeleton-number"></div>
                <div class="skeleton skeleton-line"></div>
            </div>
        @endfor
    </div>

    <div class="skeleton-table">
        <div class="skeleton-table-heading">
            <div class="skeleton skeleton-line"></div>
            <div class="skeleton skeleton-line"></div>
            <div class="skeleton skeleton-line"></div>
        </div>

        @for($i = 0; $i < 5; $i++)
            <div class="skeleton-table-row">
                <div class="skeleton skeleton-line"></div>
                <div class="skeleton skeleton-line"></div>
                <div class="skeleton skeleton-line"></div>
            </div>
        @endfor
    </div>

</div>
