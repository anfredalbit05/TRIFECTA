<?php

namespace App\Livewire\Concerns;

/**
 * Shared loading skeleton for lazily-loaded tab components.
 *
 * Every admin / member tab renders its Livewire component with `lazy`,
 * so Livewire swaps in whatever this trait returns until the request
 * finishes. Keeping the markup in one partial means all tabs show the
 * exact same shimmer instead of every component repeating a
 * `placeholder()` method.
 *
 *     use HasTabSkeleton;
 *
 * That is all a component needs — the skeleton view is resolved for it.
 */
trait HasTabSkeleton
{
    public function placeholder()
    {
        return view('livewire.placeholders.tab-skeleton');
    }
}
