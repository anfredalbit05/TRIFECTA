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
        'CreatedBy', // audit: users.UserID of whoever recorded the row
        'created_at', // optional manual record date (editable while Unposted only)
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

    /** The user who created (recorded) this disbursement. */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'CreatedBy', 'UserID');
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
     * The ledger row mirroring this disbursement's fund:
     * per fee fund — Source 'DB', RefID = FeeID (one rollup row per fund).
     */
    public function ledgerEntry()
    {
        if (! $this->FeeID) {
            return null;
        }

        return Ledger::where('Source', 'DB')
            ->where('RefID', $this->FeeID)
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
        // Creation + every lawful update re-rolls the fee fund's single
        // ledger mirror (created / edited / posted / voided alike). Editing
        // a fund that changed re-rolls BOTH funds — money moved out of one
        // and into the other.
        static::saved(function (Disbursement $disbursement) {
            $originalFee = $disbursement->getOriginal('FeeID');

            if ($originalFee && $originalFee !== $disbursement->FeeID) {
                static::syncLedgerForFee($originalFee);
            }

            static::syncLedgerForFee((string) $disbursement->FeeID);
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
     * The fee fund's single ledger mirror (Source 'DB', RefID = FeeID):
     *
     *   amount  = -(total non-void disbursements of the fund)
     *   status  = Cancelled when every disbursement is voided (fund intact)
     *             Pending   while any non-void row is still Unposted
     *             Completed once every non-void row is Posted
     *
     * Rolling up per FUND (instead of per disbursement) keeps RefID equal
     * to the membership_fees ID so ledger reports can attribute DB
     * expenses straight to a fee, with no identical duplicate rows and
     * no lost mirrors when a disbursement is edited.
     */
    public static function syncLedgerForFee(string $feeId): void
    {
        $rows = static::query()
            ->where('FeeID', $feeId)
            ->whereIn('status', [self::STATUS_UNPOSTED, self::STATUS_POSTED])
            ->get(['amount', 'status']);

        $status = $rows->isEmpty()
            ? 'Cancelled'
            : ($rows->contains(fn ($row) => (int) $row->status === self::STATUS_UNPOSTED)
                ? 'Pending'
                : 'Completed');

        Ledger::updateOrCreate(
            [
                'Source' => 'DB',
                'RefID' => $feeId,
            ],
            [
                'amount' => -(float) $rows->sum('amount'),
                'status' => $status,
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
