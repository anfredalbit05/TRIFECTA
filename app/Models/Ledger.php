<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ledger extends Model
{
    use HasFactory;

    protected $table = 'ledgers';
    protected $primaryKey = 'TransactionID';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'RefID',
        'WithdrawID',
        'amount',
        'Source',
        'ActivationID', // <--- Add this line
        'DrawID', // Spin the Wheel cycle that produced this entry (Source PK)
        'status',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    /**
     * The Spin the Wheel cycle this expense belongs to (Source 'PK' only).
     */
    public function draw(): BelongsTo
    {
        return $this->belongsTo(SpinDraw::class, 'DrawID', 'DrawID');
    }

    /**
     * The disbursement a legacy mirror row pointed at (Source 'DB' with
     * a numeric RefID = DID). New mirrors roll up per fee fund instead
     * (RefID = FeeID), so this resolves only rows saved before that
     * change; a helper rather than a belongsTo relation because
     * ledgers.RefID is a varchar business ref shared by several sources
     * and an ORM relation would silently match unrelated rows in
     * cross-database tests.
     */
    public function disbursement(): ?Disbursement
    {
        if ($this->Source !== 'DB' || ! ctype_digit((string) $this->RefID)) {
            return null;
        }

        return Disbursement::find((int) $this->RefID);
    }
}
