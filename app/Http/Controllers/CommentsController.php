<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\Response;
use Illuminate\Http\JsonResponse;
use App\Models\Comments;
use App\Http\Resources\CommentResource;
use App\Http\Requests\CommentRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\Posts;

class CommentsController extends Controller
{
    //
    /**
     * List comments by post function
     *
     * @param [type] $post_id
     * @param Request $request
     * @return JsonResponse
     */
    public function viewByPost($post_id, Request $request): JsonResponse
    {
        $posts = Posts::find((int) $post_id);
        if (empty($posts)) {
            return Response::error(message: "Comment not found");
        }
        $perPage = $request->integer('per_page', 10);
        $comments = Comments::with('user')
            ->where('post_id', $post_id)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->withQueryString();
        $data = CommentResource::collection($comments);
        return Response::success(data: $data, additional:[
            'meta' => [
                'current_page' => $comments->currentPage(),
                'from' => $comments->firstItem(),
                'last_page' => $comments->lastPage(),
                'path' => $comments->path(),
                'per_page' => $comments->perPage(),
                'to' => $comments->lastItem(),
                'total' => $comments->total(),
            ],
            'links' => [
                'first' => $comments->url(1),
                'last' => $comments->url($comments->lastPage()),
                'prev' => $comments->previousPageUrl(),
                'next' => $comments->nextPageUrl(),
            ],
        ]);
    }
    /**
     * detail comment function
     *
     * @param [type] $id
     * @param CommentRequest $request
     * @return JsonResponse
     */
    public function show($id, CommentRequest $request): JsonResponse
    {
        $comment = Comments::with('user')->where('id', $id)->first();
        if (empty($comment)) {
            return Response::error(message: "Comment not found");
        }
        return Response::success(data: CommentResource::make($comment));
    }
    /**
     * store comment
     *
     * @param CommentRequest $request
     * @return JsonResponse
     */
    public function store(CommentRequest $request): JsonResponse
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
        $post_id = $formData['post_id'];
        $content = $formData['content'];

        $user = Auth::user();
        $id_user = $user->id;

        $post = Comments::create([
            'post_id' => $post_id,
            'content' => $content,
            'user_id' => $id_user
        ]);

        return Response::success(data: CommentResource::make($post));
    }
    /**
     * update comment
     *
     * @param [type] $id
     * @param CommentRequest $request
     * @return JsonResponse
     */
    public function update($id, CommentRequest $request): JsonResponse
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
        $comment = Comments::find((int) $id);
        if (empty($comment)) {
            return Response::error(message: "Comment not found");
        }
        $formData = $request->validated();
        $content = $formData['content'];
        $comment = Comments::where('id', $id)->update([
            'content' => $content
        ]);

        return Response::success(message: "success update data");
    }
    /**
     * delete comment
     *
     * @param [type] $id
     * @param CommentRequest $request
     * @return JsonResponse
     */
    public function delete($id, CommentRequest $request): JsonResponse
    {
        $comment = Comments::find($id);
        if (empty($comment)) {
            return Response::error(message: "Comment not found");
        }
        $comment->delete();
        return Response::success(message: "delete success!");
    }
}
