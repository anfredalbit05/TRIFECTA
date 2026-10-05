<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'isFree',

    ];

    /*
    |--------------------------------------------------------------------------
    | Attribute Casts
    |--------------------------------------------------------------------------
    */

    protected function casts(): array
    {
        return [
            // Free members provisioned when a satellite activation is
            // approved (everything else is a paid activation).
            'isFree' => 'boolean',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Member Being Activated
    |--------------------------------------------------------------------------
    */

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
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
    | Policy Number Generation
    |
    | Sequential TRI-00550-TS2026-550 style numbers. The sequence STARTS at
    | 550 when nothing has been issued yet, and continues from the HIGHEST
    | sequence found in BOTH the activations and users tables (users gets a
    | copy of the policy number on approval, so scanning only one table
    | could reuse a number). Backlog numbers below the next number remain
    | allowed as long as they are unused — same rule as receipt numbers.
    |--------------------------------------------------------------------------
    */

    /** First policy sequence used when no TRI- number has been issued yet. */
    public const FIRST_POLICY_SEQUENCE = 550;

    /**
     * The highest policy sequence issued for the current year, across the
     * activations AND users tables. 0 when none has been issued yet.
     */
    public static function latestPolicySequence(): int
    {
        $year = now()->year;
        $highest = 0;

        $tables = [
            static::query()->select('policy_number'),
            User::query()->select('policy_number'),
        ];

        foreach ($tables as $table) {
            foreach ($table
                ->whereNotNull('policy_number')
                ->where('policy_number', 'like', 'TRI-%-TS'.$year.'-%')
                ->pluck('policy_number') as $policyNumber) {
                if (preg_match('/^TRI-(\d+)-TS\d{4}-(\d+)$/', (string) $policyNumber, $matches)) {
                    // The first and final numbers must match anyway; take the
                    // larger of the two so a malformed legacy row can never
                    // make the sequence repeat a number.
                    $sequence = max((int) $matches[1], (int) $matches[2]);

                    if ($sequence > $highest) {
                        $highest = $sequence;
                    }
                }
            }
        }

        return $highest;
    }

    /**
     * The next policy number to be generated, e.g. TRI-00551-TS2026-551.
     * Starts at TRI-00550-TS2026-550 when nothing has been issued yet.
     */
    public static function nextPolicyNumber(): string
    {
        $next = max(static::FIRST_POLICY_SEQUENCE, static::latestPolicySequence() + 1);
        $year = now()->year;

        return sprintf('TRI-%05d-TS%d-%d', $next, $year, $next);
    }

    /*
    |--------------------------------------------------------------------------
    | Referral / Upline User
    |--------------------------------------------------------------------------
    */

    /**
     * @return BelongsTo<User, $this>
     */
    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'ProcessedBy',
            'UserID'
        );
    }

    /**
     * @return HasMany<ActivationHistory, $this>
     */
    public function histories(): HasMany
    {
        return $this->hasMany(
            ActivationHistory::class,
            'ActivationID',
            'ActivationID'
        );
    }
}
