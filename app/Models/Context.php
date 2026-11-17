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

    /**
     * Built-in TRIFECTA copy used whenever the contexts table is empty
     * or a particular key has not been saved yet.
     */
    public static function defaults(): array
    {
        return config('trifecta-content', []);
    }

    /**
     * Database copy layered on top of the built-in defaults.
     * DB values always win; missing keys fall back to TRIFECTA copy.
     */
    public static function merged(): array
    {
        return array_merge(
            self::defaults(),
            self::query()->pluck('details', 'context_key')->toArray()
        );
    }
}
