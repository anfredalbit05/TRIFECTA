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
        'name',
        // Add any other fillable columns your 'roles' table contains (e.g., description)
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
