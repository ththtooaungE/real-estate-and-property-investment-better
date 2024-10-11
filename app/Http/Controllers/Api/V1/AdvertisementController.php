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
use Illuminate\Support\Facades\Log;

class AdvertisementController extends Controller
{
    use ResponseFormattable;

    private $advLimit;
    private $perPage;
    private $fileService;

    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
        $this->advLimit = config('const.advertisements.advertisementLimit');
        $this->perPage = config('const.advertisements.perPage');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $perPage = request('per_page') ?? $this->perPage;

            $advertisements = Advertisement::latest('id')
                ->filter(request(['is_active']))
                ->paginate($perPage)
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
            $quantity = Advertisement::where('end', '>', now())->count();

            if ($quantity >= $this->advLimit) {
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
            $validated['photo'] = $this->handlePhotoUpload($advertisement, $validated['photo'] ?? null);

            if ($advertisement->update($validated)) {
                return $this->successResponse('success', 'Advertisement successfully updated!', 200);
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

            return $this->successResponse('success', 'Advertisement successfully deleted!', 200);
        } catch (Exception $e) {
            Log::error($e);
            return $this->errorResponse('fail', 'Something went wrong!', 500);
        }
    }


    private function handlePhotoUpload($advertisement, $photo)
    {
        if ($photo) {
            $this->fileService->deletePhoto($advertisement->photo);
            return $this->fileService->storePhoto($photo, 'advertisements');
        }
        return $advertisement->photo;
    }
}
