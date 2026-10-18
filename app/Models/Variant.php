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

    /**
     * One variant can have many activations.
     */
    public function activations(): HasMany
    {
        return $this->hasMany(
            Activation::class,
            'VID',
            'VID'
        );
    }

        /**
     * One variant can have many membership fees.
     */
    public function membershipFees(): HasMany
    {
        return $this->hasMany(
            MembershipFee::class,
            'VID',
            'VID'
        );
    }
}
