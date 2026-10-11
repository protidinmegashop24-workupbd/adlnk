<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BlogPost extends Model
{
    protected $fillable = ['title', 'meta_title', 'slug', 'excerpt', 'meta_description', 'body', 'body_mode', 'body_raw', 'category', 'thumbnail', 'published', 'published_at', 'author_id'];

    protected function casts(): array
    {
        return [
            'published' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('published', true);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(BlogComment::class);
    }

    public function approvedComments(): HasMany
    {
        return $this->comments()->where('approved', true)->oldest();
    }

    /**
     * Finds YouTube video IDs from <iframe> embeds in the post body, so the
     * page can emit matching VideoObject structured data automatically —
     * Google's Video indexing report needs this schema to index embedded
     * video, a plain iframe alone usually isn't enough.
     *
     * @return array<int, string>
     */
    public function youtubeVideoIds(): array
    {
        if (! preg_match_all('#youtube(?:-nocookie)?\.com/embed/([a-zA-Z0-9_-]{11})#i', $this->body, $matches)) {
            return [];
        }

        return array_values(array_unique($matches[1]));
    }
}
