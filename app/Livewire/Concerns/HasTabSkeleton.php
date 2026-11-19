<?php

namespace App\Livewire\Concerns;

use Illuminate\Support\Str;

/**
 * Shared loading skeleton for lazily-loaded tab components.
 *
 * Every admin / member tab renders its Livewire component with `lazy`,
 * so Livewire swaps in whatever this trait returns until the request
 * finishes.
 *
 * The skeleton has to look like the tab it is standing in for, so each
 * component resolves its own view from its class path:
 *
 *     App\Livewire\AdminPages\ReportSettings\GeneralSettings
 *         -> livewire.placeholders.skeletons.admin-pages.report-settings.general-settings
 *
 * A component without its own skeleton file falls back to the generic
 * `livewire.placeholders.tab-skeleton` partial.
 *
 *     use HasTabSkeleton;
 *
 * That is all a component needs.
 */
trait HasTabSkeleton
{
    public function placeholder()
    {
        return view($this->skeletonView());
    }

    /** The skeleton view for this component, or the generic fallback. */
    protected function skeletonView(): string
    {
        $candidate = 'livewire.placeholders.skeletons.'.$this->skeletonKey();

        return view()->exists($candidate) ? $candidate : 'livewire.placeholders.tab-skeleton';
    }

    /**
     * Class path turned into a view key:
     * `App\Livewire\AdminPages\Masterlist\Roles` -> `admin-pages.masterlist.roles`.
     */
    protected function skeletonKey(): string
    {
        return collect(explode('\\', Str::after(static::class, 'App\\Livewire\\')))
            ->map(fn (string $segment) => Str::kebab($segment))
            ->implode('.');
    }
}
