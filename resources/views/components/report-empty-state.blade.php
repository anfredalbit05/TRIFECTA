
@props([
    'title' => 'No Records Found',
    'message' => 'There are currently no records to display.',
    'icon' => '📂'
])

<div class="report-empty-state">
    <div class="report-empty-icon">
        {{ $icon }}
    </div>

    <h3>{{ $title }}</h3>

    <p>{{ $message }}</p>
</div>
