<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
        | Satellite row this activation belongs to (satellite memberships)
        */

        'satelliteID',

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

        'upline_code',

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

        /*
        | Acknowledgement receipt number
        | (auto-generated on approval, e.g. 000550)
        */

        'receipt_number',

        /*
        | Policy details (Cocogen) attached to this activation —
        | the users table has no such columns.
        */

        'policy_number',
        'cocogen_policy_number',
        'policy_effectivity_date',
        'policy_expiration_date',

        /*
        | Cashier who signed the acknowledgement receipt
        | (name + signature image path, snapshot at approval)
        */

        'cashier_name',
        'cashier_signature',

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
    | Receipt Number Generation
    |
    | Sequential 6-digit numbers (000550, 000551, ...) based on the HIGHEST
    | control number in the activations table — regardless of which row or
    | status holds it — so a number is never reused. When no number has
    | been issued yet, the sequence starts at 000550.
    |--------------------------------------------------------------------------
    */

    public static function nextReceiptNumber(): string
    {
        // Zero-padded 6-digit numbers sort identically as strings and ints,
        // so MAX(receipt_number) is the highest number ever issued.
        $highest = static::query()
            ->whereNotNull('receipt_number')
            ->max('receipt_number');

        $next = ($highest === null)
            ? 550
            : ((int) $highest + 1);

        return str_pad((string) $next, 6, '0', STR_PAD_LEFT);
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

    public function histories()
    {
        return $this->hasMany(
            ActivationHistory::class,
            'ActivationID',
            'ActivationID'
        );
    }
}
