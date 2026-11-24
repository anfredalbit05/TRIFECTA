<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Disbursement extends Model
{
    protected $table = 'disburstments';

    protected $primaryKey = 'DID';

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'FeeID',
        'amount',
        'remarks',
        'CreatedBy', // audit: users.UserID of whoever recorded the row
        'created_at', // record date
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    /** Membership-fee fund this disbursement draws from (FeeID is a varchar code). */
    public function fee(): BelongsTo
    {
        return $this->belongsTo(MembershipFee::class, 'FeeID', 'FeeID');
    }

    /** The user who recorded this disbursement. */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'CreatedBy', 'UserID');
    }

    /** Total disbursed out of a fee fund. */
    public static function disbursedForFee(string $feeId): float
    {
        return (float) static::where('FeeID', $feeId)->sum('amount');
    }
}
