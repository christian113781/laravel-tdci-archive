<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Archive extends Model
{
    use HasFactory;

    protected $fillable = [
        'archive_code',
        'title',
        'authors',
        'subject',
        'year',
        'program_id',
        'category',
        'user_id',
        'thesis_file',
        'tables_file',
        'recommendation_file',
        'figures_file',
        'status',
        'citation',
    ];

    // 🔑 Keywords relation
    public function keywords()
    {
        return $this->belongsToMany(
            Keyword::class,      // Related model
            'archive_keyword',   // Pivot table
            'archive_id',        // Foreign key on pivot for Archive
            'keyword_id'         // Foreign key on pivot for Keyword
        )->withTimestamps();
    }

    // (optional) Program relation
    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    // (optional) User relation
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bookmarks()
{
    return $this->hasMany(\App\Models\Bookmark::class);
}

public function accessRequests()
    {
        return $this->hasMany(\App\Models\ArchiveAccessRequest::class, 'archive_id');
    }

 protected static function booted()
    {
        static::creating(function ($archive) {
            $datePart = now()->format('ymd');        // e.g., “251020”
            $prefix   = 'ARC-' . $datePart . '-';

            // find last one with today’s prefix
            $last = self::where('archive_code', 'like', $prefix . '%')
                        ->orderBy('archive_code', 'desc')
                        ->first();

            if ($last) {
                $lastSeq = (int) substr($last->archive_code, strrpos($last->archive_code, '-') + 1);
                $nextSeq = $lastSeq + 1;
            } else {
                $nextSeq = 1;
            }

            $seqPart = str_pad($nextSeq, 3, '0', STR_PAD_LEFT);   // “001”, “002”, etc.
            $archive->archive_code = $prefix . $seqPart;
        });
    }


     /**
     * Check if the user can view this archive.
     * Returns true if:
     *   - category = 'A' (general access) OR
     *   - category = 'B' AND user has a request with status 'approved'
     *
     * @param int $userId
     * @return bool
     */
    public function userCanView(int $userId): bool
    {
        if ($this->category === 'A') {
            return true;
        }

        if ($this->category === 'B') {
            return $this->accessRequests()
                        ->where('user_id', $userId)
                        ->where('status', 'approved')
                        ->exists();
        }

        return false;
    }

    /**
     * Check if the user has requested access (pending or approved).
     *
     * @param int $userId
     * @return bool
     */
    public function userHasRequested(int $userId): bool
    {
        return $this->accessRequests()
                    ->where('user_id', $userId)
                    ->whereIn('status', ['pending','approved'])
                    ->exists();
    }
    
}

