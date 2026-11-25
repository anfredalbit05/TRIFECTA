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
     protected $table = 'beneficiaries';
     protected $guarded = [];
     protected $casts = ['birthdate' => 'date:Y-m-d'];

    public function user()
    {
        return $this->belongsTo(
            User::class,
            'UserID',
            'UserID'
        );
    }


}
