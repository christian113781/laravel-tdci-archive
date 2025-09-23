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
        'abstract',
        'year',
        'program_id',
        'category',
        'user_id',
        'file_path',
        'status',
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
}

