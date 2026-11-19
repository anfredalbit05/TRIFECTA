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
    | Format: TF-00001
    | Starts at 00001 if no receipt number exists.
    |--------------------------------------------------------------------------
    */

    public const FIRST_RECEIPT_SEQUENCE = 1;

    public static function nextReceiptNumber(): string
    {
        $highest = 0;

        $receiptNumbers = static::query()
            ->whereNotNull('receipt_number')
            ->pluck('receipt_number');

        foreach ($receiptNumbers as $receiptNumber) {
            if (preg_match(
                '/^TF-(\d+)$/',
                trim((string) $receiptNumber),
                $matches
            )) {
                $sequence = (int) $matches[1];

                if ($sequence > $highest) {
                    $highest = $sequence;
                }
            }
        }

        $next = max(
            static::FIRST_RECEIPT_SEQUENCE,
            $highest + 1
        );

        return sprintf('TF-%05d', $next);
    }


    public const FIRST_POLICY_SEQUENCE = 1;

    /**
     * Get the highest policy sequence issued for the current year.
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
                ->pluck('policy_number');

            foreach ($policyNumbers as $policyNumber) {
                if (preg_match(
                    '/^TF-(\d+)-TF(\d{4})-(\d+)$/',
                    trim((string) $policyNumber),
                    $matches
                )) {
                    if ((int) $matches[2] !== $year) {
                        continue;
                    }

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
     */
    public static function nextPolicyNumber(): string
    {
        $next = max(
            static::FIRST_POLICY_SEQUENCE,
            static::latestPolicySequence() + 1
        );

        $year = now()->year;

        return sprintf(
            'TF-%05d-TF%d-%05d',
            $next,
            $year,
            $next
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
