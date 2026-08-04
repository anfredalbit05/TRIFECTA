<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ledger extends Model
{
    use HasFactory;

    protected $table = 'ledgers';
    protected $primaryKey = 'TransactionID';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'RefID',
        'amount',
        'Source',
        'ActivationID', // <--- Add this line
        'status',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];
}
