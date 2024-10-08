<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'description',
        'street',
        'township',
        'city',
        'state_or_division',
        'price',
        'width',
        'length',
        'status',
        'is_declined'
    ];


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(Photo::class);
    }

    public function boosts(): HasMany
    {
        return $this->hasMany(Boost::class);
    }

    public function scopeBoosted($query)
    {
        $query->select('posts.*', 'boosts.equalizer')
            ->join('boosts', 'posts.id', '=', 'boosts.post_id')
            ->where('boosts.end', '>', now()->format('Y-m-d H:i:s'))
            ->orderBy('boosts.equalizer', 'asc')
            ->distinct();
    }

    public function scopeFilter($query, array $filter)
    {
        /**
         * @param string $description
         */
        $query->when($filter['description'] ?? null, function ($query, $description) {
            $query->where('description', 'LIKE', '%' . $description . '%');
        });

        /**
         * township is excepted
         * @param string $township
         */
        $query->when($filter['township'] ?? null, function ($query, $township) {
            $query->where('township', 'LIKE', '%' . $township . '%');
        });

        /**
         * city is excepted
         * @param string $city
         */
        $query->when($filter['city'] ?? null, function ($query, $city) {
            $query->where('city', 'LIKE', '%' . $city . '%');
        });

        /**
         * stateOrDivision is excepted
         * @param string $stateOrDivision
         */
        $query->when($filter['state_or_division'] ?? null, function ($query, $stateOrDivision) {
            $query->where('state_or_division', 'LIKE', '%' . $stateOrDivision . '%');
        });

        /**
         * "sell" or "rent" is excepted
         * @param string $status
         */
        $query->when($filter['status'] ?? null, function ($query, $status) {
            $query->where('status', $status);
        });

        /**
         * any string is excepted
         * @param string $width
         */
        $query->when($filter['width'] ?? null, function ($query, $width) {
            $query->where('width', 'LIKE', '%' . $width . '%');
        });

        /**
         * @param string $length
         */
        $query->when($filter['length'] ?? null, function ($query, $length) {
            $query->where('length', 'LIKE', '%' . $length . '%');
        });

        /**
         * get the declined posts or undeclined posts
         * @param boolean $is_declined 
         */
        $query->when(isset($filter['is_declined']), function ($query) use ($filter) {
            $is_declined = filter_var($filter['is_declined'], FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
            $query->where('is_declined', $is_declined);
        });

        /**
         * get the admin's posts or agent posts
         * @param boolean $is_admin
         */
        $query->when(isset($filter['is_admin']), function ($query) use ($filter) {
            $is_admin = filter_var($filter['is_admin'], FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
            $query->whereHas('user', function ($query) use ($is_admin) {
                $query->where('is_admin', $is_admin);
            });
        });
    }
}
