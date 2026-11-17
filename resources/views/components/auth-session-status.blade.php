@props([
    'status',
])

@if ($status)
    <div {{ $attributes->merge(['class' => 'font-medium text-sm text-[var(--color-gold-darker)]']) }}>
        {{ $status }}
    </div>
@endif
