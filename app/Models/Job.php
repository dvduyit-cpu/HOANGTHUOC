<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Job extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'slug', 'location', 'employment_type', 'salary', 'summary',
        'description', 'requirements', 'benefits', 'contact_email', 'image_path',
        'status', 'expires_at', 'published_at', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'date',
            'published_at' => 'datetime',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    public function scopePublic($query)
    {
        return $query
            ->where('status', 'published')
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhereDate('expires_at', '>=', today());
            });
    }
}
