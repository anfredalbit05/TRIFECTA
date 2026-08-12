<?php

namespace App\Traits;

use App\Models\Privilege;

trait ChecksMemberPermissions
{
    protected function callerHasReadPermission($caller): bool
    {
        if (!$caller) {
            return false;
        }

        if ((int) $caller->RoleID === 1) {
            return true;
        }

        return Privilege::where('RoleID', $caller->RoleID)
            ->where('ModuleID', 2)
            ->where('can_read', 1)
            ->exists();
    }
}
