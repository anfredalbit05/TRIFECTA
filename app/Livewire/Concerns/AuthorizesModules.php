<?php

namespace App\Livewire\Concerns;

use App\Support\Modules;

/**
 * Global permission access for Livewire components.
 *
 * A component declares the single module it manages...
 *
 *     use AuthorizesModules;
 *
 *     protected function module(): int
 *     {
 *         return Modules::MEMBERS;
 *     }
 *
 * ...and instantly gets computed properties the blades can use:
 * $this->canCreate, $this->canRead, $this->canUpdate,
 * $this->canDelete, $this->canPrint, $this->canChangeStatus
 * (plus ->can('update') for anything conditional).
 *
 * Components that juggle two modules (e.g. a modal reused from both
 * the Withdrawal and Activation tabs) implement modules(): array
 * instead and receive per-module checks via ->canFor($action, $id).
 */
trait AuthorizesModules
{
    /** ModuleID this component manages. Override in the component. */
    protected function module(): int
    {
        return defined('static::MODULE_ID') ? static::MODULE_ID : 0;
    }

    /** All ModuleIDs this component manages (default: just module()). */
    protected function modules(): array
    {
        return array_filter([$this->module()]);
    }

    /** Check an action against this component's declared module. */
    public function can(string $action): bool
    {
        return $this->canFor($action, $this->module());
    }

    /** Check an action against an explicit ModuleID. */
    public function canFor(string $action, int $moduleId): bool
    {
        return Modules::can($moduleId, $action);
    }

    public function getCanCreateProperty(): bool
    {
        return $this->canAny('create');
    }

    public function getCanReadProperty(): bool
    {
        return $this->canAny('read');
    }

    public function getCanUpdateProperty(): bool
    {
        return $this->canAny('update');
    }

    public function getCanDeleteProperty(): bool
    {
        return $this->canAny('delete');
    }

    public function getCanPrintProperty(): bool
    {
        return $this->canAny('print');
    }

    public function getCanChangeStatusProperty(): bool
    {
        return $this->canAny('change_status');
    }

    /** True when ANY declared module allows the action (multi-module components). */
    public function canAny(string $action): bool
    {
        foreach ($this->modules() as $moduleId) {
            if (Modules::can($moduleId, $action)) {
                return true;
            }
        }

        return false;
    }
}
