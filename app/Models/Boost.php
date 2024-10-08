<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Boost extends Model
{
    use HasFactory;

    protected $fillable = ['post_id', 'equalizer', 'start', 'end'];

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }
}
