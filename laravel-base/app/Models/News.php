<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    protected $fillable = ['title_vi', 'title_en', 'slug', 'excerpt_vi', 'excerpt_en', 'content_vi', 'content_en', 'cover_image', 'status', 'published_at'];

    protected function casts(): array
    {
        return ['published_at' => 'datetime'];
    }

    public function localized(string $field): string
    {
        $english = $this->getAttribute($field.'_en');

        return app()->getLocale() === 'en' && filled($english)
            ? $english
            : (string) $this->getAttribute($field.'_vi');
    }

    public function scopePublic($query)
    {
        return $query->where('status', 'published')->whereNotNull('published_at')->where('published_at', '<=', now());
    }
}
