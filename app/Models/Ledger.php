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
}
