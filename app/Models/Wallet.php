<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    protected $primaryKey = 'WalletID';

    protected $fillable = [
        'UserID',
        'ActivationID',
        'amount',
        'source', // <--- ADD THIS LINE HERE
        'status',
    ];


    public function user()
    {
        return $this->belongsTo(
            User::class,
            'UserID',
            'UserID'
        );
    }


    public function activation()
    {
        return $this->belongsTo(
            Activation::class,
            'ActivationID',
            'ActivationID'
        );
    }
}
