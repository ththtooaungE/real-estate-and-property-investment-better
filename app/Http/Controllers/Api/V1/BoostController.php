<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\BoostCreateRequest;
use App\Http\Requests\BoostUpdateRequest;
use App\Http\Resources\PostResource;
use App\Models\Boost;
use App\Models\Post;
use App\Traits\ApiResponseFormatter;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BoostController extends Controller
{
    use ApiResponseFormatter;


    /**
     * Store a newly created resource in storage.
     */
    public function store(BoostCreateRequest $request, Post $post)
    {
        try {
            if ($post->boosts()->where('end', '>', now()->format('Y-m-d H:i:s'))->count())
                return $this->errorResponse('fail', 'Active Boost existed!', 422);

            Boost::query()->update(['equalizer' => 0]);

            if ($post->boosts()->create($request->validated()))
                return $this->successResponse('success', 'Boost successfully created', 201, new PostResource($post));
        } catch (Exception $e) {
            Log::error($e);
            return $this->errorResponse('fail', 'Something went wrong!', 500);
        }
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(BoostUpdateRequest $request, Post $post, string $boost_id)
    {
        try {
            $boost = $post->boosts()->where('end', '>', now()->format('Y-m-d H:i:s'))->find($boost_id);

            if (!$boost)
                return $this->successResponse('fail', 'Wrong Boost Record or Expired!', 422);

            if ($boost->update($request->validated()))
                return $this->successResponse('success', 'Boost successfully updated', 204);
        } catch (Exception $e) {
            Log::error($e);
            return $this->errorResponse('fail', 'Something went wrong!', 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post, string $boost_id)
    {
        try {
            if (!auth()->user()->is_admin)
                return $this->errorResponse('fail', 'Must Be Admin!', 403);

            $boost = $post->boosts()->where('end', '>', now()->format('Y-m-d H:i:s'))->find($boost_id);

            if (!$boost)
                return $this->successResponse('fail', 'Wrong Boost Record or Expired!', 422);

            if ($boost->delete())
                return $this->successResponse('success', 'Boost successfully deleted!', 204);
        } catch (Exception $e) {
            Log::error($e);
            return $this->errorResponse('fail', 'Something went wrong!', 500);
        }
    }
}
