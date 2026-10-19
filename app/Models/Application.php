<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Application extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_id', 'name', 'email', 'phone', 'cover_letter', 'cv_path',
        'cv_original_name', 'status', 'email_sent_at',
    ];

    protected function casts(): array
    {
        return [
            'email_sent_at' => 'datetime',
        ];
    }

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }
}
