<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    protected $primaryKey = 'BranchID';

    protected $fillable = [
        'branchCode',
        'branchname',
        'type',
        'address',
        'status',
        'UserID',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'UserID', 'UserID');
    }
}
