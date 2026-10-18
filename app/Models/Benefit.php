<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Benefit extends Model
{
    protected $primaryKey = 'BentID';

    protected $fillable = [

        'name',

        'details',

        'VID',

        'amount',

        'holdertype',

        'status',

    ];

    public function beneficiaries()
    {
        return $this->hasMany(
            Beneficiary::class,
            'BenefitID',
            'BentID'
        );
    }
}
