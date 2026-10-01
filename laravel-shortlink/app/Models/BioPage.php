<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BioPage extends Model
{
    protected $fillable = ['user_id', 'slug', 'title', 'links'];

    protected function casts(): array
    {
        return [
            'links' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
