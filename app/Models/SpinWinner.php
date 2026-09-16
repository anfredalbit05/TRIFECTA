<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpinWinner extends Model
{
    protected $table = 'spin_winners';

    protected $primaryKey = 'WinnerID';

    protected $fillable = [
        'DrawID',
        'UserID',
        'spin_order',
        'qualified_invites',
        'prize_name',
        'prize_amount',
        'DrawnBy',
    ];

    protected $casts = [
        'prize_amount' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'UserID', 'UserID');
    }

    public function draw()
    {
        return $this->belongsTo(SpinDraw::class, 'DrawID', 'DrawID');
    }

    public function drawnBy()
    {
        return $this->belongsTo(User::class, 'DrawnBy', 'UserID');
    }

    /**
     * The Pangkabuhayan expense booked for this prize (Source 'PK', negative
     * amount) — linked only through the ledger's DrawID + RefID, never stored
     * here. SpinWheelService::winnersOf() attaches it by spin order; its
     * status is the claim state: Pending until the prize is tagged as
     * claimed, then Completed.
     */
    public function ledger()
    {
        return $this->hasOne(Ledger::class, 'DrawID', 'DrawID')
            ->where('Source', 'PK')
            ->where('RefID', (string) $this->UserID)
            ->orderBy('TransactionID')
            ->skip(max(0, (int) $this->spin_order - 1));
    }
}
