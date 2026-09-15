<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Satellite extends Model
{
    protected $primaryKey = 'satelliteID';

    protected $fillable = [
        'satellite_name',
        'address',
        'latitude',
        'longitude',
        'UserID',
        'satellite_typeID',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'UserID', 'UserID');
    }
}
