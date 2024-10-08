<?php

namespace App\Services;

use App\Models\Post;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;

class PostService
{
    private $fileService;

    function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
    }
    /**
     * get active boosted posts
     */
    public function getBootedPosts($perPage)
    {
        $boosted_posts = Post::boosted()
            ->filter(request(['status'])) // "status" filter will be applied on boosted posts too
            ->limit($perPage)
            ->paginate($perPage);

        $postIds = $boosted_posts->pluck('id');
        DB::table('boosts')->whereIn('post_id', $postIds)->increment('equalizer'); // Update the equalizer for all boosts associated with the retrieved post IDs

        return $boosted_posts;
    }

    /**
     * Retrieve normal posts excluding the active boosted ones
     * they could be boosted but must be expired
     */
    public function getUnBoostedPosts($perPage, $sortBy, $order)
    {
        $posts = Post::where(function ($query) {
            $query->whereDoesntHave('boosts')
                ->orWhereHas('boosts', function ($query) {
                    $query->where('end', '<', now());
                });
        })
            ->orderBy($sortBy, $order)
            ->filter(request(['description', 'township', 'city', 'state_or_division', 'status', 'width', 'length', 'is_declined', 'is_admin']))
            ->paginate($perPage)
            ->withQueryString();

        return $posts;
    }


    /**
     * $total is needed. We don't take total number from $data.
     * Cause $data is conbined 3 of boosted posts and 7 of normal posts. Numbers could be changed.
     */
    public function makeCustomnPagination($data, $perPage, $total)
    {
        $currentPage = LengthAwarePaginator::resolveCurrentPage(); // Get the page automatically from URL
        return new LengthAwarePaginator(
            $data->forPage(1, $perPage),
            $total,
            $perPage,
            $currentPage,
            ['path' => LengthAwarePaginator::resolveCurrentPath()]
        );
    }

    /**
     * Return the data in an array format
     */
    public function processStoringPhotos(object $post, array $photos): object
    {
        $photos_array = array_map(function ($photo) {
            return ['name' => $this->fileService->storePhoto($photo, 'posts')];
        }, $photos);

        return $post->photos()->createMany($photos_array);
    }


    /**
     * Return the data in an array
     */
    public function processStoringPhoto(object $post, $photo): object
    {
        $photo_array = ['name' => $this->fileService->storePhoto(data: $photo, location: 'posts')];
        return $post->photos()->create($photo_array);
    }


    /**
     * Delete the image data
     */
    public function deletePhoto($photo)
    {
        $this->fileService->deletePhoto(location: $photo->name);
    }


    /**
     * Delete the image data and record
     */
    public function deletePhotos($photos)
    {
        foreach ($photos as $photo) {
            $this->fileService->deletePhoto(location: $photo->name);
            $photo->delete();
        }
    }
}
