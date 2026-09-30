<?php

namespace App\Traits;

use App\Support\Modules;

trait ChecksMemberPermissions
{
    /**
     * Read permission on member records via the global module system
     * (Masterlist - Members, ModuleID 2).
     */
    protected function callerHasReadPermission($caller): bool
    {
        if (! $caller) {
            return false;
        }

        return Modules::can(Modules::MEMBERS, 'read', $caller);
    }
}
