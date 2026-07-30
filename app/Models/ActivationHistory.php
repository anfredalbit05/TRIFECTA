<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivationHistory extends Model
{
    protected $table = 'activation_histories';

    protected $primaryKey = 'HistoryID';

    protected $fillable = [
        'ActivationID',
        'AdminID',
        'status',
        'remarks',
    ];

    public function activation()
    {
        return $this->belongsTo(
            Activation::class,
            'ActivationID',
            'ActivationID'
        );
    }

    public function admin()
    {
        return $this->belongsTo(
            User::class,
            'AdminID',
            'UserID'
        );
    }
}
