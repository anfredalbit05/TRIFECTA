<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Module extends Model
{
    use HasFactory;

    protected $table = 'modules';
    protected $primaryKey = 'ModuleID';

    protected $fillable = [
        'name',
        // Add any other fillable columns your 'modules' table contains (e.g., slug, description)
    ];

    /**
     * Get the privileges associated with the module.
     */
    public function privileges()
    {
        return $this->hasMany(Privilege::class, 'ModuleID', 'ModuleID');
    }
}
