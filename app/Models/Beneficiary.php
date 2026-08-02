<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Beneficiary extends Model
{
    protected $primaryKey = 'BeneficiaryID';

    protected $fillable = [

        'UserID',

        'fullname',

        'birthdate',

        'relationship',

        'status',

    ];

    public function user()
    {
        return $this->belongsTo(
            User::class,
            'UserID',
            'UserID'
        );
    }


}
