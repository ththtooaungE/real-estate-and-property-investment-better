<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\AboutStoreRequest;
use App\Models\About;
use App\Traits\ApiResponseFormatter;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AboutController extends Controller
{
    use ApiResponseFormatter;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $per_page = request()->query('per_page') ?? 10;
            $abouts = About::latest('id')->paginate($per_page);
            return $this->paginatedSuccessResponse('success', 'About successfully retrieved!', 200, $abouts);
        } catch (Exception $e) {
            Log::error($e);
            return $this->errorResponse('fail', 'Something went wrong!', 500);
        }
    }

    public function upsert(AboutStoreRequest $request)
    {
        try {
            $about = About::latest('id')->first();

            if ($about) {
                $about->update($request->validated());
                return $this->successResponse('success', 'About successfully updated!', 200, $about);
            }

            $about = About::create($request->validated());
            return $this->successResponse('success', 'About successfully created!', 201, $about);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return $this->errorResponse('fail', $e->getMessage(), 500);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(About $about)
    {
        try {
            return $this->successResponse('success', 'About successfully retrieved!', 200, $about);
        } catch (Exception $e) {
            Log::error($e);
            return $this->errorResponse('fail', 'Something went wrong!', 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(About $about)
    {
        try {
            if (!auth()->user()->is_admin) return $this->errorResponse('fail', 'Must Be Admin!', 403);

            $about->delete();
            return $this->successResponse('success', 'About successfully deleted!', 204);
        } catch (Exception $e) {
            Log::error($e);
            return $this->errorResponse('fail', 'Something went wrong!', 500);
        }
    }
}