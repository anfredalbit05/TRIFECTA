<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $table = 'roles';
    protected $primaryKey = 'RoleID';

    protected $fillable = [
        'rolename',
        'description',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    /**
     * Get the privileges associated with the role.
     */
    public function privileges()
    {
        return $this->hasMany(Privilege::class, 'RoleID', 'RoleID');
    }

    /**
     * Get the users assigned to this role.
     */
    public function users()
    {
        return $this->hasMany(User::class, 'RoleID', 'RoleID');
    }
}
