<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class CocogenMemberInsurance extends Model
{
    protected $table = 'cocogen_members_insurance';

    protected $primaryKey = 'CocogenID';

    protected $fillable = [
        'UserID',
        'member_name',
        'beneficiary_name',
        'address',
        'birthdate',
        'civil_status',
        'gender',
        'occupation',
        'contact_number',
        'plan',
        'amount',
    ];

    protected $casts = [
        'birthdate' => 'date',
        'amount' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'UserID', 'UserID');
    }
}
