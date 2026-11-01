<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'name',
    'description',
    'status',
])]
class Variant extends Model
{
    use HasFactory;

    protected $primaryKey = 'VID';

    public function activations(): HasMany
    {
        return $this->hasMany(
            Activation::class,
            'VID',
            'VID'
        );
    }

    public function membershipFees(): HasMany
    {
        return $this->hasMany(
            MembershipFee::class,
            'VID',
            'VID'
        );
    }

    public function branchGrants(): HasMany
    {
        return $this->hasMany(
            BranchVariantGrants::class,
            'VID',
            'VID'
        );
    }
}
