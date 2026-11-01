<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use LogicException;

class Disbursement extends Model
{
    protected $table = 'Disburstment';

    protected $primaryKey = 'DID';

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'FeeID',
        'amount',
        'remarks',
        'status',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'status' => 'integer',
    ];

    /** 0 = Unposted (record exists, ledger row Pending). */
    public const STATUS_UNPOSTED = 0;

    /** 1 = Posted (ledger row Completed, expense live). */
    public const STATUS_POSTED = 1;

    /** 2 = Void (ledger row Cancelled — final state, no un-post). */
    public const STATUS_VOID = 2;

    /**
     * Membership-fee fund this disbursement draws from.
     * The FK target's key is a VARCHAR business code ('FEE001'…).
     */
    public function fee(): BelongsTo
    {
        return $this->belongsTo(MembershipFee::class, 'FeeID', 'FeeID');
    }

    /**
     * Ledger status that mirrors this disbursement's status:
     * 0 (Unposted) -> Pending, 1 (Posted) -> Completed, 2 (Void) -> Cancelled.
     */
    public static function ledgerStatusFor(int $status): string
    {
        return match ($status) {
            self::STATUS_UNPOSTED => 'Pending',
            self::STATUS_POSTED => 'Completed',
            default => 'Cancelled',
        };
    }

    /**
     * The ledger row this disbursement mirrors:
     * Source 'DB', RefID = DID, amount NEGATIVE (expenses).
     */
    public function ledgerEntry()
    {
        return Ledger::where('Source', 'DB')
            ->where('RefID', (string) $this->DID)
            ->first();
    }

    /**
     * Total already disbursed out of a fee fund (void rows excluded:
     * cancelled money never left the fund).
     */
    public static function disbursedForFee(string $feeId): float
    {
        return (float) static::where('FeeID', $feeId)
            ->where('status', '!=', self::STATUS_VOID)
            ->sum('amount');
    }

    protected static function booted(): void
    {
        // Creation + every lawful update keeps the ledger mirror in step.
        static::saved(function (Disbursement $disbursement) {
            $disbursement->syncLedger();
        });

        static::updating(function (Disbursement $disbursement) {
            $original = $disbursement->getOriginal();

            // 1) Rows that left Unposted are locked: their financial fields
            //    freeze. The status may still move (0 -> 1 post, 0/1 -> 2
            //    void) and Remarks stay editable so a void reason can always
            //    be appended to the audit trail.
            // Rows inserted without an explicit status rely on the DB
            // default (0), so the attribute may be missing from original.
            $originalStatus = (int) ($original['status'] ?? self::STATUS_UNPOSTED);

            if ($originalStatus !== self::STATUS_UNPOSTED) {
                $locked = collect($disbursement->getDirty())
                    ->keys()
                    ->reject(fn ($field) => in_array($field, ['DID', 'status', 'remarks', 'updated_at']));

                if ($locked->isNotEmpty()) {
                    throw new LogicException(
                    'A '.self::statusLabel($originalStatus).
                    ' disbursement can no longer be edited. Void it and create a new one if the details changed.'
                    );
                }
            }

            // 2) Status can never move backwards: 1 -> 0 (un-post),
            //    2 -> 0 and 2 -> 1 are forbidden because the ledger would
            //    silently flip a cancelled/live expense back to Pending.
            $from = $originalStatus;
            $to = (int) ($disbursement->status ?? self::STATUS_UNPOSTED);

            if ($from !== $to && $to < $from) {
                throw new LogicException(
                    'A '.self::statusLabel($from).' disbursement cannot go back to '.self::statusLabel($to).'.'
                );
            }
        });
    }

    /**
     * Create (or keep in step) the mirrored ledger entry:
     * one row per disbursement, Source 'DB', RefID = DID, negative amount.
     */
    public function syncLedger(): void
    {
        Ledger::updateOrCreate(
            [
                'Source' => 'DB',
                'RefID' => (string) $this->DID,
            ],
            [
                'amount' => -$this->amount,
                'status' => self::ledgerStatusFor((int) $this->status),
            ]
        );
    }

    public static function statusLabel(int $status): string
    {
        return match ($status) {
            self::STATUS_UNPOSTED => 'Unposted',
            self::STATUS_POSTED => 'Posted',
            default => 'Voided',
        };
    }
}
