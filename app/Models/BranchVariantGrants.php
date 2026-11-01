<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BranchVariantGrants extends Model
{
    protected $table = 'branch_variant_grants';

    protected $primaryKey = 'GrantID';

    protected $fillable = [
        'BranchID',
        'VID',
        'status',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(
            Branch::class,
            'BranchID',
            'BranchID'
        );
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(
            Variant::class,
            'VID',
            'VID'
        );
    }
}
