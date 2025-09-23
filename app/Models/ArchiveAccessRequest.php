<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArchiveAccessRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'archive_id',
        'status',
        'approved_by',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function archive()
    {
        return $this->belongsTo(Archive::class, 'archive_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
