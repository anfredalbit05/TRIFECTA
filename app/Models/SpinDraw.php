<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpinDraw extends Model
{
    protected $table = 'spin_draws';

    protected $primaryKey = 'DrawID';

    protected $fillable = [
        'draw_date',
        'period_start',
        'period_end',
        'pangkabuhayan_total',
        'prize_name',
        'prize_amount',
        'winners_target',
        'allow_repeat_winners',
        'status',
        'completed_at',
        'AnnouncementID',
    ];

    protected $casts = [
        'draw_date' => 'date',
        'period_start' => 'date',
        'period_end' => 'date',
        'pangkabuhayan_total' => 'decimal:2',
        'prize_amount' => 'decimal:2',
        'allow_repeat_winners' => 'boolean',
        'completed_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Winners drawn for this cycle
    |--------------------------------------------------------------------------
    */

    public function winners()
    {
        return $this->hasMany(SpinWinner::class, 'DrawID', 'DrawID')
            ->orderBy('spin_order');
    }

    /*
    |--------------------------------------------------------------------------
    | Dashboard announcement listing this cycle's winners
    |--------------------------------------------------------------------------
    */

    public function announcement()
    {
        return $this->belongsTo(NewsUpdate::class, 'AnnouncementID', 'ID');
    }
}
