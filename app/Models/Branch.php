<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    protected $primaryKey = 'BranchID';

    protected $fillable = [
        'branchCode',
        'branchname',
        'address',
        'status',
        'UserID',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'boolean',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'UserID', 'UserID');
    }
}
