<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\Response;
use Illuminate\Http\JsonResponse;
use App\Models\User;
use App\Http\Resources\UserResource;

class UsersController extends Controller
{
    //
    /**
     * List controller
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->integer('per_page', 10);
        $users = User::orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->withQueryString();
        $data = UserResource::collection($users);
        return Response::success(data: $data, additional:[
            'meta' => [
                'current_page' => $users->currentPage(),
                'from' => $users->firstItem(),
                'last_page' => $users->lastPage(),
                'path' => $users->path(),
                'per_page' => $users->perPage(),
                'to' => $users->lastItem(),
                'total' => $users->total(),
            ],
            'links' => [
                'first' => $users->url(1),
                'last' => $users->url($users->lastPage()),
                'prev' => $users->previousPageUrl(),
                'next' => $users->nextPageUrl(),
            ],
        ]);
    }
    /**
     * show controller
     *
     * @param Request $request
     * @param [type] $id
     * @return JsonResponse
     */
    public function show(Request $request, $id): JsonResponse
    {
        $user = User::find($id);
        if (empty($user)) {
            return Response::error(message: "user not found");
        }
        $data = UserResource::make($user);
        return Response::success(data: $data);
    }
}
