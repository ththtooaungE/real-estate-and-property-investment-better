<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Advertisement extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'owner', 'photo', 'start', 'end'];


    public function scopeFilter($query, array $filter)
    {
        $query->when($filter['is_active'] ?? null, function ($query) use ($filter) {
            $is_active = filter_var($filter['is_active'], FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
            if ($is_active) {
                $query->where('end', '>', now()->format('Y-m-d H:i:s'));
            }
        });
    }
}
