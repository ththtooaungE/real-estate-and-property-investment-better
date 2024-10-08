<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Mail\ForgotPassword;
use App\Models\User;
use App\Traits\ApiResponseFormatter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    use ApiResponseFormatter;

    public function register(RegisterRequest $request)
    {
        try {
            $validated = $request->validated();

            $validated['password'] = Hash::make($validated['password']);
            if ($validated['is_agent'] ?? false) $validated['status'] = 'pending';
            // return $validated;
            User::create($validated);

            return response()->json([
                'message' => 'Successfully Registered!',
                'data' => [],
                'meta' => []
            ], 200);
        } catch (\Exception $e) {
            logger()->error($e);
            return response()->json([
                'message' => 'Something went wrong',
            ], 500);
        }
    }

    public function login(LoginRequest $request)
    {
        try {
            $validated = $request->validated();

            $user = User::where('email', $validated['email'])->first();

            if (!$user) {
                return response()->json(['message' => 'Incorrect Credential!',], 401);
            }

            if (!$user || !Hash::check($validated['password'], $user->password)) {
                return $this->errorResponse('fail', 'Incorrect credentials!', 401);
            }

            if ($user->is_admin) {
                $user->token = $user->createToken("admin-token")->plainTextToken;
            } elseif ($user->is_agent) {
                $user->token = $user->createToken("agent-token")->plainTextToken;
            } else {
                $user->token = $user->createToken("user-token")->plainTextToken;
            }


            return $this->successResponse('success', 'Successfully Logged In!', 200, new UserResource($user));
        } catch (\Exception $e) {
            logger()->error($e);
            return response()->json(['message' => 'Something went wrong',], 500);
        }
    }


    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return $this->successResponse('success', 'Successfully logged out!', 200, []);
    }


    public function refresh()
    {
        $user = User::find(Auth::user()->id);
        $user->tokens()->delete();
        return $this->successResponse('success', 'All Tokens are successfull refreshed!', 200, []);
    }

    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ]);

        DB::table('password_reset_tokens')
            ->where('email', $request->input('email'))
            ->delete();

        $token = rand(100001, 999999);
        DB::table('password_reset_tokens')
            ->insert([
                'email' => $request->input('email'),
                'token' => $token,
                'expired_at' => now()->addMinutes(10),
                'created_at' => now()
            ]);

        Mail::to($request->input('email'))->send(new ForgotPassword($token));

        return $this->successResponse('success', 'Reset Password Link is sent to your email!', 200);
    }

    public function verifyResetPasswordToken($token)
    {
        try {
            $user = DB::table('password_reset_tokens')
                ->where('token', $token)
                ->where('expired_at', '>', now())
                ->first();

            if (!$user) {
                return $this->errorResponse('fail', 'Invalid token!', 401);
            };

            return $this->successResponse('success', 'Token is valid!', 200, []);
            // return view('mail.reset-password-mail-demo', ["token" => $token]);
        } catch (\Exception $e) {
            logger()->error($e);
            return $this->errorResponse('fail', 'Something went wrong!', 500);
        }
    }

    public function resetPassword(Request $request)
    {
        try {
            $request->validate([
                'password' => 'required|string|min:6|max:20|confirmed',
                'password_confirmation' => 'required',
                'token' => 'required|string'
            ]);

            $valid_token = DB::table('password_reset_tokens')
                ->where('token', $request->input('token'))
                ->where('expired_at', '>', now())
                ->first();

            if (!$valid_token) return $this->errorResponse('fail', 'Invalid token!', 401);

            $user = User::where('email', $valid_token->email)->first();

            if ($user->update(['password' => Hash::make($request->input('password'))])) {

                DB::table('personal_access_tokens')->where('tokenable_id', $user->id)->delete();
                DB::table('password_reset_tokens')->where('token', $request->input('token'))->delete();

                return $this->successResponse('success', 'Your password is updated!', 200);
            } else {
                return $this->errorResponse('fail', 'Something went wrong!', 500);
            }
        } catch (\Exception $e) {
            logger()->error($e);
            return $this->errorResponse('fail', 'Something went wrong!', 500);
        }
    }
}
