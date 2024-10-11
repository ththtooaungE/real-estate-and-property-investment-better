<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileUpdateRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Resources\UserResource;
use App\Services\FileService;
use App\Traits\ResponseFormattable;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ProfileController extends Controller
{
    use ResponseFormattable;
    protected $fileService;

    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
    }
    /**
     * Show a resource
     */
    public function show()
    {
        try {
            return $this->successResponse('success', 'Profile is successfully retrived!', 200, new UserResource(auth()->user()));
        } catch (Exception $e) {
            Log::error($e);
            return $this->errorResponse('fail', 'Something went wrong!', 500);
        }
    }


    /**
     * Update a resource
     */
    public function update(UserUpdateRequest $request)
    {
        try {
            /**@var \App\Models\User $user*/
            $user = Auth::user();
            if ($user->update($request->validated())) {
                return $this->successResponse('success', 'Profile is successfully updated!', 200);
            }
        } catch (Exception $e) {
            Log::error($e);
            return $this->errorResponse('fail', 'Something went wrong!', 500);
        }
    }

    /**
     * Update a resource
     */
    public function updatePhoto(ProfileUpdateRequest $request)
    {
        try {
            $photo = auth()->user()->photo;
            if ($photo) $this->fileService->deletePhoto($photo);

            $path = $this->fileService->storePhoto($request->input('photo'), 'profiles');

            /**@var \App\Models\User $user*/
            $user = Auth::user();
            if ($user->update(['photo' => $path])) {
                return $this->successResponse('success', 'Profile Photo is successfully updated!', 200);
            }
        } catch (Exception $e) {
            Log::error($e);
            return $this->errorResponse('fail', 'Something went wrong!', 500);
        }
    }
}
