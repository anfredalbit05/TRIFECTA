<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NewsUpdate extends Model
{
    protected $table = 'news_updates';

    protected $primaryKey = 'ID';


    protected $fillable = [
        'title',
        'details',
        'image',
        'category',
        'status',
        'UserID',
    ];


    public function user(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'UserID',
            'UserID'
        );
    }
}
