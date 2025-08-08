<?php

namespace App\Http\Controllers\Api\v2;

use App\Http\Controllers\Controller;
use App\Http\Requests\PostStoreRequest;
use App\Http\Requests\PostUpdateRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $query = Post::query()
            ->when(! $user->hasRole('admin'), fn($q) => $q->where('user_id', $user->id))
            ->when($request->filled('title'), fn($q) => $q->where('title', 'like', "%{$request->title}%"))
            ->when($request->filled('category_id'), fn($q) => $q->where('category_id', $request->category_id))
            ->when($request->filled('tagged_user_id'), function ($q) use ($request) {
                $q->whereHas('taggedUsers', fn($tag) => $tag->where('user_id', $request->tagged_user_id));
            })
            ->latest();

        return PostResource::collection($query->paginate(10));
    }

    public function store(PostStoreRequest $request)
    {
        $post = Post::create([
            'title'       => $request->title,
            'body'        => $request->body,
            'user_id'     => $request->user()->id,
            'category_id' => $request->category_id,
        ]);

        $taggedUserIds = $this->extractTaggedUserIds($request->body);
        if ($taggedUserIds) {
            $post->taggedUsers()->sync($taggedUserIds);
        }

        return new PostResource($post);
    }

    public function show(Post $post)
    {
        $this->authorize('view', $post);
        return new PostResource($post);
    }

    public function update(PostUpdateRequest $request, Post $post)
    {
        $this->authorize('update', $post);

        $post->update($request->validated());

        if ($request->has('body')) {
            $taggedUserIds = $this->extractTaggedUserIds($request->body);
            $post->taggedUsers()->sync($taggedUserIds);
        }

        return new PostResource($post);
    }

    public function destroy(Post $post)
    {
        $this->authorize('delete', $post);
        $post->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Post soft deleted.'
        ], 200);
    }

    public function trashed()
    {
        $posts = Post::onlyTrashed()->get();
        return PostResource::collection($posts);
    }

    public function restore($id)
    {
        $post = Post::onlyTrashed()->findOrFail($id);
        $this->authorize('restore', $post);
        $post->restore();

        return response()->json([
            'status'  => 'success',
            'message' => 'Post restored successfully'
        ], 200);
    }

    public function forceDelete($id)
    {
        $post = Post::onlyTrashed()->findOrFail($id);
        $this->authorize('forceDelete', $post);
        $post->forceDelete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Post permanently deleted'
        ], 200);
    }

    private function extractTaggedUserIds(string $body): array
    {
        preg_match_all('/@([\w]+)/', $body, $matches);
        $usernames = $matches[1] ?? [];
        return User::whereIn('name', $usernames)->pluck('id')->toArray();
    }
}
