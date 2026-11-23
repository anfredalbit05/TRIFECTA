<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Branch extends Model
{
    protected $table = 'branches';

    protected $primaryKey = 'BranchID';

    protected $fillable = [
        'branchName',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'BranchID', 'BranchID');
    }
}
