<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MembershipFee extends Model
{
    use HasFactory;

    protected $table = 'membership_fees';

    protected $primaryKey = 'FeeID';

    // If FeeID is a string (e.g., 'FEE001'), set incrementing to false and keyType to string
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'FeeID',
        'FeeName',
        'Cost',
        'VID',
    ];

    protected $casts = [
        'Cost' => 'decimal:2',
    ];

        /**
     * Each membership fee belongs to one variant.
     */
    public function variant(): BelongsTo
    {
        return $this->belongsTo(
            Variant::class,
            'VID',
            'VID'
        );
    }
}
