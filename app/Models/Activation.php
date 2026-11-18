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
        'UserID',
        'satelliteID',
        'VID',
        'amount',
        'type',
        'payment_method',
        'upline_code',
        'payment_reference',
        'ProcessedBy',
        'status',
        'receipt_number',
        'policy_number',
        'cocogen_policy_number',
        'policy_effectivity_date',
        'policy_expiration_date',
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
            'isFree' => 'boolean',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Member Being Activated
    |--------------------------------------------------------------------------
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
    | Format: 00001, 00002, 00003...
    | Starts at 00001 if no receipt number exists.
    |--------------------------------------------------------------------------
    */

    public const FIRST_RECEIPT_SEQUENCE = 1;

    public static function nextReceiptNumber(): string
    {
        $highest = static::query()
            ->whereNotNull('receipt_number')
            ->max('receipt_number');

        $next = ($highest === null)
            ? static::FIRST_RECEIPT_SEQUENCE
            : ((int) $highest + 1);

        return str_pad(
            (string) $next,
            5,
            '0',
            STR_PAD_LEFT
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Policy Number Generation
    |
    | Format: TRI-00001-2026-1
    |
    | TRI     = Prefix
    | 00001   = Sequential policy number
    | 2026    = Current year
    | 1       = Policy identifier / final segment
    |
    | The sequence checks BOTH activations and users tables.
    |--------------------------------------------------------------------------
    */

    public const FIRST_POLICY_SEQUENCE = 1;

    /**
     * Get the highest policy sequence issued for the current year.
     *
     * Searches both activations and users tables.
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
            $policyNumbers = $table
                ->whereNotNull('policy_number')
                ->where('policy_number', 'like', 'TRI-%-' . $year . '-%')
                ->pluck('policy_number');

            foreach ($policyNumbers as $policyNumber) {
                if (
                    preg_match(
                        '/^TRI-(\d+)-(\d{4})-(\d+)$/',
                        (string) $policyNumber,
                        $matches
                    )
                ) {
                    // Only use the sequence segment.
                    // The final segment is NOT the sequence.
                    $sequence = (int) $matches[1];

                    if ($sequence > $highest) {
                        $highest = $sequence;
                    }
                }
            }
        }

        return $highest;
    }

    /**
     * Generate the next policy number.
     *
     * Example:
     * TRI-00001-2026-1
     * TRI-00002-2026-1
     * TRI-00003-2026-1
     */
    public static function nextPolicyNumber(): string
    {
        $next = max(
            static::FIRST_POLICY_SEQUENCE,
            static::latestPolicySequence() + 1
        );

        $year = now()->year;

        return sprintf(
            'TRI-%05d-%d-1',
            $next,
            $year
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Referral / Upline User
    |--------------------------------------------------------------------------
    */

    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'ProcessedBy',
            'UserID'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Activation Histories
    |--------------------------------------------------------------------------
    */

    public function histories(): HasMany
    {
        return $this->hasMany(
            ActivationHistory::class,
            'ActivationID',
            'ActivationID'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Variant
    |--------------------------------------------------------------------------
    */

    public function variant(): BelongsTo
    {
        return $this->belongsTo(
            Variant::class,
            'VID',
            'VID'
        );
    }
}
