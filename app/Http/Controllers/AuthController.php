<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\Response;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\AuthRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Jobs\SendWelcomeEmailJob;

class AuthController extends Controller
{
    //
    private $auth_token = 'simple-blog-apps';
    /**
     * Register function
     *
     * @param AuthRequest $request
     * @return JsonResponse
     */
    public function register(AuthRequest $request): JsonResponse
    {
        if ($request->errors) {
            $glue = "";
            $message = "";
            foreach ($request->errors->all() as $error) {
                $message .= $glue . $error;
                $glue = " , ";
            }
            return Response::error($request->errors, $message, 422);
        }
        $formData = $request->validated();

        $name = $formData['name'];
        $email = $formData['email'];
        $password = bcrypt($formData['password']);

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => $password
        ]);
        SendWelcomeEmailJob::dispatch($user->id);
        $result = [
            'user' => $user,
            'token' => $user->createToken($this->auth_token)->plainTextToken
        ];
        return Response::success(data: $result);
    }

    /**
     * Login function
     *
     * @param AuthRequest $request
     * @return JsonResponse
     */
    public function login(AuthRequest $request): JsonResponse
    {
        if ($request->errors) {
            $glue = "";
            $message = "";
            foreach ($request->errors->all() as $error) {
                $message .= $glue . $error;
                $glue = " , ";
            }
            return Response::error($request->errors, $message, 422);
        }
        $formData = $request->validated();
        $email = $formData['email'];
        $password = $formData['password'];

        $user = User::where('email', $email)->first();
        if (empty($user)) {
            return Response::error(message: 'Your requested email is not registered yet', code: 401);
        }

        if (!Hash::check($password, $user->password, [])) {
            return Response::error(message: "Email and password doesnt match", code: 401);
        }

        $tokenResult = $user->createToken($this->auth_token)->plainTextToken;
        $result = [
            'access_token' => $tokenResult,
            'token_type' => 'Bearer'
        ];
        return Response::success(data: $result);
    }

    public function logout(Request $request): JsonResponse
    {
        $user = $request->user();
        if (empty($user)) {
            return Response::error(message: "User already logout");
        }
        $user->tokens()->delete();
        return Response::success(message: 'Logout success!');
    }

    public function me(Request $request): JsonResponse
    {
        $user = Auth::user();
        return Response::success(data: $user);
    }
}
