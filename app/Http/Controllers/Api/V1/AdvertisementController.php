<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdvertisementStoreRequest;
use App\Http\Requests\AdvertisementUpdateRequest;
use App\Http\Resources\AdvertisementResource;
use App\Models\Advertisement;
use App\Services\FileService;
use App\Traits\ResponseFormattable;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AdvertisementController extends Controller
{
    use ResponseFormattable;

    private $maxNum = 5;
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
            $per_page = request()->query('per_page') ?? 10;

            $advertisements = Advertisement::latest('id')
                ->filter(request(['is_active']))
                ->paginate($per_page)
                ->withQueryString();

            return $this->paginatedSuccessResponse('success', 'Advertisements successfully retrieved!', 200, AdvertisementResource::collection($advertisements));
        } catch (Exception $e) {
            Log::error($e);
            return $this->errorResponse('fail', 'Something went wrong!', 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AdvertisementStoreRequest $request)
    {
        try {
            $validated = $request->validated();

            if ((Advertisement::where('end', '>', now())->count()) >= $this->maxNum) {
                return $this->errorResponse('fail', 'Limit Excedded!', 422);
            }

            $validated['photo'] = $this->fileService->storePhoto($validated['photo'], 'advertisements');
            $advertisement = Advertisement::create($validated);

            if ($advertisement) {
                return $this->successResponse('success', 'Advertisement successfully created!', 201, new AdvertisementResource($advertisement));
            } else {
                throw new Exception('Something went wrong!', 500);
            }
        } catch (Exception $e) {
            Log::error($e);
            return $this->errorResponse('fail', 'Something went wrong!', 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Advertisement $advertisement)
    {
        try {
            return $this->successResponse('success', 'Advertisement successfully retrived!', 200, new AdvertisementResource($advertisement));
        } catch (Exception $e) {
            Log::error($e);
            return $this->errorResponse('fail', 'Something went wrong!', 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AdvertisementUpdateRequest $request, Advertisement $advertisement)
    {
        try {
            if ($advertisement->end < now()) return $this->errorResponse('fail', 'Already Expired', 422);

            $validated = $request->validated();
            if ($validated['photo'] ?? null) {
                $this->fileService->deletePhoto($advertisement->photo);
                $validated['photo'] = $this->fileService->storePhoto(data: $validated['photo'], location: 'advertisements');
            }

            if ($advertisement->update($validated)) {
                return $this->successResponse('success', 'Advertisement successfully updated!', 204);
            }
        } catch (Exception $e) {
            Log::error($e);
            return $this->errorResponse('fail', 'Something went wrong!', 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Advertisement $advertisement)
    {
        try {
            $this->fileService->deletePhoto($advertisement->photo);
            $advertisement->delete();

            return $this->successResponse('success', 'Advertisement successfully deleted!', 204);
        } catch (Exception $e) {
            Log::error($e);
            return $this->errorResponse('fail', 'Something went wrong!', 500);
        }
    }
}
