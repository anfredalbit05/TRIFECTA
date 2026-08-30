<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Withdrawal extends Model
{
    use HasFactory;

    protected $table = 'withdrawals';
    protected $primaryKey = 'WithdrawID';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'UserID',
        'amount',
        'type',
        'withdrawal_method',
        'account_name',
        'account_number',
        'payment_reference',
        'ProcessedBy',
        'status',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];
}
