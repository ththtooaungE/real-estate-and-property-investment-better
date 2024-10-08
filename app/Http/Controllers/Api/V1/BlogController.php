<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\BlogStoreRequest;
use App\Http\Requests\BlogUpdateRequest;
use App\Http\Resources\BlogResource;
use App\Models\Blog;
use App\Services\FileService;
use App\Traits\ApiResponseFormatter;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class BlogController extends Controller
{
    use ApiResponseFormatter;
    private $paginationLimit = 10;
    private $fileService;

    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $sortBy = request()->query('sort_by', 'id');
            $order = request()->query('order', 'desc');

            if (!in_array($order, ['asc', 'desc'])) {
                return $this->errorResponse('fail', 'Invalid order parameter!', 400);
            }

            $blogs = Blog::with('user')
                ->orderBy($sortBy, $order)
                ->filter(request(['search']))
                ->paginate(request('per_page') ?? $this->paginationLimit)
                ->withQueryString();

            return $this->paginatedSuccessResponse('success', 'Blogs successfully retrieved!', 200, BlogResource::collection($blogs));
        } catch (Exception $e) {
            Log::error($e);
            return $this->errorResponse('fail', 'Something went wrong!', 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BlogStoreRequest $request)
    {
        try {
            $validated_data = $request->validated();
            $validated_data['photo'] = $this->fileService->storePhoto(data: $validated_data['photo'], location: 'blogs');
            $validated_data['user_id'] = auth()->user()->id;

            $blog = Blog::create($validated_data);

            if ($blog) {
                return $this->successResponse('success', 'Blog successfully created!', 201, new BlogResource($blog));
            }
        } catch (Exception $e) {
            Log::error($e);
            return $this->errorResponse('fail', 'Something went wrong!', 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Blog $blog)
    {
        try {
            return $this->successResponse('success', 'Blog successfully retrieved!', 200, new BlogResource($blog));
        } catch (Exception $e) {
            Log::error($e);
            return $this->errorResponse('fail', 'Something went wrong!', 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BlogUpdateRequest $request, Blog $blog)
    {
        try {
            $validated_data = $request->validated();

            if ($validated_data['photo'] ?? null) {
                $this->fileService->deletePhoto($blog->photo);
                $validated_data['photo'] = $this->fileService->storePhoto(data: $validated_data['photo'], location: 'blogs');
            }

            if ($blog->update($validated_data)) {
                return $this->successResponse('success', 'Blog successfully updated!', 204);
            }
        } catch (Exception $e) {
            Log::error($e);
            return $this->errorResponse('fail', 'Something went wrong!', 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Blog $blog)
    {
        try {
            if (!auth()->user()->is_admin) {
                return $this->errorResponse('fail', 'Must Be Admin!', 403);
            }

            $this->fileService->deletePhoto($blog->photo);

            if ($blog->delete()) {
                return $this->successResponse('success', 'Blog successfully deleted!', 204);
            }
        } catch (Exception $e) {
            Log::error($e);
            return $this->errorResponse('fail', 'Something went wrong!', 500);
        }
    }
}
