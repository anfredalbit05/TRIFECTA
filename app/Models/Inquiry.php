<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inquiry extends Model
{
    use HasFactory;

    protected $table = 'inquiries';

    protected $primaryKey = 'ID';

    protected $fillable = [
        'name',
        'email',
        'subject',
        'details',
        'category',
        'date_read',
        'AssignedUserID',
    ];

    protected $casts = [
        'date_read' => 'datetime',
    ];

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'AssignedUserID', 'UserID');
    }

    public function isRead(): bool
    {
        return $this->date_read !== null;
    }

    public function markAsRead(): void
    {
        $this->update([
            'date_read' => now(),
        ]);
    }
}
