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
        'requested_amount',
        'amount',
        'tax_amount',
        'type',
        'withdrawal_method',
        'bank_name', // <--- Added here
        'account_name',
        'account_number',
        'payment_reference',
        'ProcessedBy',
        'status',
        'confirmed_by'
    ];

    protected $casts = [
        'requested_amount' => 'decimal:2',
        'amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
    ];
}
