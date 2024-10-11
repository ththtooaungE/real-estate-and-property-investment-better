<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserDeleteRequest;
use App\Http\Requests\UserUpdateStatusRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Traits\ApiResponseFormatter;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    use ApiResponseFormatter;
    protected $paginationLimit = 10;

    public function index(Request $request)
    {
        try {
            $perPage = $request->query('per_page') ?? $this->paginationLimit;
            $users = UserResource::collection(
                User::latest('id')
                    ->filter(request(['search']))
                    ->paginate($perPage)
                    ->withQueryString()
            );

            return $this->paginatedSuccessResponse('success', 'Users are successfully retrieved!', 200, $users);
        } catch (Exception $e) {
            Log::error($e);
            return $this->errorResponse('fail', 'Something went wrong!', 500);
        }
    }

    public function show(User $user)
    {
        try {
            return $this->successResponse('success', 'User is successfully retrieved!', 200, new UserResource($user));
        } catch (Exception $e) {
            Log::error($e);
            return $this->errorResponse('fail', 'Something went wrong!', 500);
        }
    }


    public function destroy(UserDeleteRequest $request, User $user)
    {
        try {
            if ($user->delete()) { // cascade on delete with posts
                return $this->successResponse('success', 'User is successfully deleted!', 204, []);
            }
        } catch (Exception $e) {
            Log::error($e);
            return $this->errorResponse('fail', 'Something went wrong!', 500);
        }
    }

    /**
     * Agent Models will be shown
     */
    public function agentIndex(Request $request)
    {
        try {
            $perPage = $request->query('perPage') ?? $this->paginationLimit;
            $agents = UserResource::collection(
                User::filter(request(['search', 'status']))->where('is_agent', true)->latest('id')->paginate($perPage)
            );

            return $this->paginatedSuccessResponse('success', 'Agents are successfully retrieved!', 200, $agents);
        } catch (Exception $e) {
            Log::error($e);
            return $this->errorResponse('fail', 'Something went wrong!', 500);
        }
    }

    /**
     * Agent Model and related posts
     */
    public function agentShow(String $id)
    {
        try {
            $agent = User::getAgent($id)->first();
            if (!$agent) throw new ModelNotFoundException('Agent Not Found!', 404);

            return $this->successResponse('success', 'Agent is successfully retrieved!', 200, new UserResource($agent));
        } catch (ModelNotFoundException $e) {
            return $this->errorResponse('fail', 'Agent Not Found!', 404);
        } catch (Exception $e) {
            Log::error($e);
            return $this->errorResponse('fail', 'Something went wrong!', 500);
        }
    }


    public function updateAgentStatus(UserUpdateStatusRequest $request, String $id)
    {
        try {
            $agent = User::getAgent($id)->first();
            if (!$agent) throw new ModelNotFoundException('Agent Not Found!', 404);

            if ($request->status === 'declined') $status = $agent->delete();
            else $agent->update(['status' => $status = $request->status]);

            if ($status) {
                return $this->successResponse('success', 'Agent Status is successfully updated!', 200);
            } else {
                throw new Exception('Something went wrong!');
            }
        } catch (ModelNotFoundException $e) {
            return $this->errorResponse('fail', 'Agent Not Found!', 404);
        } catch (Exception $e) {
            Log::error($e);
            return $this->errorResponse('fail', 'Something went wrong!', 500);
        }
    }


    public function countAgents()
    {
        try {
            $count = User::where('is_agent', true)
                ->where('status', 'accepted')
                ->count();
            return $this->successResponse('success', 'Agent Number is successfully retrieved!', 200, $count);
        } catch (Exception $e) {
            Log::error($e);
            return $this->errorResponse('fail', 'Something went wrong!', 500);
        }
    }
}
