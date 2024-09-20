<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\Response;
use Illuminate\Http\JsonResponse;
use App\Models\Posts;
use App\Http\Resources\PostResource;
use App\Http\Requests\PostRequest;
use Illuminate\Support\Facades\Auth;
use App\Helpers\Format;

class PostsController extends Controller
{
    //
    /**
     * List function
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->integer('per_page', 10);
        $posts = Posts::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->withQueryString();
        $data = PostResource::collection($posts);
        return Response::success(data: $data, additional:[
            'meta' => [
                'current_page' => $posts->currentPage(),
                'from' => $posts->firstItem(),
                'last_page' => $posts->lastPage(),
                'path' => $posts->path(),
                'per_page' => $posts->perPage(),
                'to' => $posts->lastItem(),
                'total' => $posts->total(),
            ],
            'links' => [
                'first' => $posts->url(1),
                'last' => $posts->url($posts->lastPage()),
                'prev' => $posts->previousPageUrl(),
                'next' => $posts->nextPageUrl(),
            ],
        ]);
    }
    /**
     * Show function
     *
     * @param [type] $id
     * @param Request $request
     * @return JsonResponse
     */
    public function show($id, Request $request): JsonResponse
    {
        $post = Posts::with(['user','comments'])->where('id', $id)->first();
        if (empty($post)) {
            return Response::error(message: "Post not found");
        }
        return Response::success(data: PostResource::make($post));
    }
    /**
     * Store function
     *
     * @param PostRequest $request
     * @return JsonResponse
     */
    public function store(PostRequest $request): JsonResponse
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
        $title = $formData['title'];
        $content = $formData['content'];

        $user = Auth::user();
        $id_user = $user->id;

        $post = Posts::create([
            'title' => $title,
            'content' => $content,
            'user_id' => $id_user,
            'slug' => Format::createUniqueSlug($title, new Posts())
        ]);

        return Response::success(data: PostResource::make($post));
    }
    /**
     * Update Function
     *
     * @param [type] $id
     * @param PostRequest $request
     * @return JsonResponse
     */
    public function update($id, PostRequest $request): JsonResponse
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
        $post = Posts::find((int) $id);
        if (empty($post)) {
            return Response::error(message: "Post not found");
        }
        $formData = $request->validated();
        $title = $formData['title'];
        $content = $formData['content'];

        $update = Posts::where('id', $id)->update([
            'title' => $title,
            'content' => $content,
            'slug' => Format::createUniqueSlug($title, new Posts())
        ]);

        return Response::success(message: "success update data");
    }
    /**
     * Delete function
     *
     * @param [type] $id
     * @param Request $request
     * @return JsonResponse
     */
    public function delete($id, Request $request): JsonResponse
    {
        $post = Posts::find($id);
        if (empty($post)) {
            return Response::error(message: "Post not found");
        }
        $post->delete();
        return Response::success(message: "delete success!");
    }
}
