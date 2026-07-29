<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Activation extends Model
{
    /*
    |--------------------------------------------------------------------------
    | Primary Key
    |--------------------------------------------------------------------------
    */

    protected $primaryKey = 'ActivationID';

    public ?User $member = null;

    /*
    |--------------------------------------------------------------------------
    | Mass Assignable Fields
    |--------------------------------------------------------------------------
    */

    protected $fillable = [

        /*
        | Member being activated
        */

        'UserID',


        /*
        | Activation amount
        */

        'amount',


        /*
        | W = Walk-in
        | O = Online
        */

        'type',


        /*
        | Cash
        | GCash
        | Bank Transfer
        */

        'payment_method',


        /*
        | Referral code entered
        | by the activating member
        */

        'referral_code',


        /*
        | GCash or bank transaction reference
        |
        | Null for Cash payments
        */

        'payment_reference',


        /*
        | UserID of the user whose
        | uplineCode matches referral_code
        */

        'ProcessedBy',


        /*
        | Pending
        | Approved
        | Rejected
        */

        'status',

    ];


    /*
    |--------------------------------------------------------------------------
    | Member Being Activated
    |--------------------------------------------------------------------------
    */

    public function user()
    {
        return $this->belongsTo(
            User::class,
            'UserID',
            'UserID'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Referral / Upline User
    |--------------------------------------------------------------------------
    */

    public function processedBy()
    {
        return $this->belongsTo(
            User::class,
            'ProcessedBy',
            'UserID'
        );
    }
}
