<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Role;

class Privilege extends Model
{
    use HasFactory;

    protected $table = 'privileges';
    protected $primaryKey = 'PrivilegeID';

    protected $fillable = [
        'RoleID',
        'ModuleID',
        'can_create',
        'can_read',
        'can_update',
        'can_delete',
        'can_print',
        'can_change_status',
    ];

    protected $casts = [
        'can_create' => 'boolean',
        'can_read' => 'boolean',
        'can_update' => 'boolean',
        'can_delete' => 'boolean',
        'can_print' => 'boolean',
        'can_change_status' => 'boolean',
    ];

    /**
     * Get the role that owns the privilege.
     */
    public function role()
    {
        return $this->belongsTo(Role::class, 'RoleID', 'RoleID');
    }

    /**
     * Get the module that owns the privilege.
     */
    public function module()
    {
        return $this->belongsTo(Module::class, 'ModuleID', 'ModuleID');
    }
}
