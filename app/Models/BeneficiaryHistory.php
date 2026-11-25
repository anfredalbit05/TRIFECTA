<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BeneficiaryHistory extends Model
{
    protected $table = 'beneficiary_histories';
    protected $primaryKey = 'HistoryID';
    const UPDATED_AT = null;
    protected $guarded = [];
}
