<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $primaryKey = 'SettingsID';

    protected $fillable = [
        'name',
        'value',
    ];

    /**
     * Mutator: Ensure boolean-like values are stored consistently as 'true' or 'false'
     * if the setting is maintenance_mode.
     */
    public function setValueAttribute($value)
    {
        if (strtolower($this->name ?? '') === 'maintenance_mode' || in_array($value, [true, false, 'true', 'false', 1, 0, '1', '0'], true)) {
            $this->attributes['value'] = filter_var($value, FILTER_VALIDATE_BOOLEAN) ? 'true' : 'false';
        } else {
            $this->attributes['value'] = $value;
        }
    }
}
