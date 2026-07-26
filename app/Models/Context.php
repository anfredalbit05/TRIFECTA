<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Context extends Model
{
    protected $primaryKey = 'ContextID';

    protected $fillable = [
        'context_key',
        'details',
    ];
}
