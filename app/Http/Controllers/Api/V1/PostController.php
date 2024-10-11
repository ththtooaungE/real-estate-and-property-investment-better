<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\PhotoStoreRequest;
use App\Http\Requests\PostStoreRequest;
use App\Http\Requests\PostUpdateRequest;
use App\Http\Resources\PhotoResource;
use App\Http\Resources\PostResource;
use App\Models\Photo;
use App\Models\Post;
use App\Models\User;
use App\Services\PostService;
use App\Traits\ResponseFormattable;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PostController extends Controller
{
    use ResponseFormattable;

    private $photoLimit = 10;
    private $boostedPerPage = 3;
    private $perPage = 10;
    private $postService = null;

    function __construct(PostService $postService)
    {
        $this->postService = $postService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $sortBy = request()->query('sort_by', 'id');
            $order = request()->query('order', 'desc');

            if (!in_array($order, ['asc', 'desc']))
                return $this->errorResponse('fail', 'Invalid order parameter!', 400);

            $boosted_posts = $this->postService->getBootedPosts($this->boostedPerPage);

            $posts = $this->postService->getUnBoostedPosts(
                perPage: ($this->perPage - $boosted_posts->count()),
                sortBy: $sortBy,
                order: $order
            );

            $combined = $boosted_posts->take(3)->merge($posts->items()); // Merge boosted posts and normal posts
            $combinedPaginated = $this->postService->makeCustomnPagination(
                data: $combined,
                perPage: $this->perPage,
                total: ($boosted_posts->total() + $posts->total())
            );

            return $this->paginatedSuccessResponse('success', 'Posts are successfully retrieved!', 200, PostResource::collection($combinedPaginated));
        } catch (Exception $e) {
            logger()->error($e);
            return $this->errorResponse('fail', 'Something went wrong!', 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PostStoreRequest $request)
    {
        DB::beginTransaction();

        try {
            $validated_data = $request->validated();
            $validated_data['user_id'] = auth()->user()->id;

            $post = Post::create($validated_data);
            $this->postService->processStoringPhotos($post, $request['photos']);

            DB::commit();
            return $this->successResponse('success', 'A post is successfully created!', 201, new PostResource($post));
        } catch (Exception $e) {
            DB::rollBack();
            logger()->error($e);
            return $this->errorResponse('fail', 'Something went wrong!', 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        try {
            $post->increment('view_count');
            return $this->successResponse('success', 'A post is successfully retrived!', 200, new PostResource($post));
        } catch (Exception $e) {
            logger()->error($e);
            return $this->errorResponse('fail', 'Something went wrong!', 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PostUpdateRequest $request, Post $post)
    {
        try {
            if ($request['photos']) $this->postService->processStoringPhotos($post, $request['photos']);

            if ($post->update($request->validated())) {
                return $this->successResponse('success', 'A post is successfully updated!', 204, []);
            } else {
                throw new Exception('Something went wrong!', 500);
            }
        } catch (Exception $e) {
            logger()->error($e);
            return $this->errorResponse('fail', 'Something went wrong!', 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        try {
            // if (auth()->user()->id !== $post->user_id)
            //     return $this->errorResponse('fail', 'Must be owner!', 403);

            DB::transaction(function () use ($post) {
                $this->postService->deletePhotos($post->photos);
                $post->photos()->delete();
                $post->delete();
            });

            return $this->successResponse('success', 'A post is successfully deleted!', 204, []);
        } catch (Exception $e) {
            logger()->error($e);
            return $this->errorResponse('fail', 'Something went wrong!', 500);
        }
    }


    /**
     * Update the resource
     */
    public function decline(Post $post)
    {
        try {
            // if (!auth()->user()->is_admin)
            //     return $this->errorResponse('fail', 'Must be Admin!', 403);

            if ($post->update(['is_declined' => true])) {
                return $this->successResponse('success', 'A post is successfully declined!', 204, []);
            } else {
                throw new Exception('Something went wrong!', 500);
            }
        } catch (Exception $e) {
            logger()->error($e);
            return $this->errorResponse('fail', 'Something went wrong!', 500);
        }
    }

    /**
     * Store the newly created resource
     */
    public function addPhoto(PhotoStoreRequest $request, Post $post)
    {
        try {
            // if (auth()->user()->id !== $post->user_id)
            //     return $this->errorResponse('fail', 'Must be owner!', 403);

            if ($post->photos()->count() >= $this->photoLimit) {
                return $this->errorResponse('fail', 'Max Quantity is 10!', 422);
            }
            $photo = $this->postService->processStoringPhoto($post, $request->validated()['photo']);
            if ($photo) {
                return $this->successResponse('success', 'A photo is successfully added!', 201, new PhotoResource($photo));
            } else {
                throw new Exception('Something went wrong!', 500);
            }
        } catch (Exception $e) {
            logger()->error($e);
            return $this->errorResponse('fail', 'Something went wrong!', 500);
        }
    }

    /**
     * Delete the specific resource
     */
    public function removePhoto(Post $post, Photo $photo)
    {
        try {
            // if (auth()->user()->id !== $post->user_id)
            //     return $this->errorResponse('fail', 'Must be owner!', 403);

            $photo_ids = $post->photos->pluck('id')->toArray();
            if (!in_array($photo->id, $photo_ids)) {
                return $this->errorResponse('fail', 'Wrong Photo!', 422);
            }

            $this->postService->deletePhoto($photo);

            if ($photo->delete()) {
                return $this->successResponse('success', 'A photo is successfully removed!', 204, []);
            } else {
                throw new Exception('Something went wrong!', 500);
            }
        } catch (Exception $e) {
            logger()->error($e);
            return $this->errorResponse('fail', 'Something went wrong!', 500);
        }
    }

    public function agentPosts(User $user)
    {
        $posts = Post::where('user_id', $user->id)->paginate(10);
        return PostResource::collection($posts);
    }
}
