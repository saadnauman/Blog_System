<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;


class PostController extends Controller
{
    // List posts with optional filters
    public function index(Request $request)
{
    $user = $request->user();

    $query = Post::query()
        ->when(! $user->hasRole('admin'), function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })
        ->when($request->filled('title'), function ($q) use ($request) {
            $q->where('title', 'like', '%' . $request->title . '%');
        })
        ->when($request->filled('category_id'), function ($q) use ($request) {
            $q->where('category_id', $request->category_id);
        })
        ->when($request->filled('tagged_user_id'), function ($q) use ($request) {
            $q->whereHas('taggedUsers', function ($tagQuery) use ($request) {
                $tagQuery->where('user_id', $request->tagged_user_id);
            });
        })
        ->latest();
        
    return PostResource::collection($query->paginate(10));
}


    // Create a new post with optional tagging
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body'  => 'required|string',
            'category_id' => 'required|exists:categories,id', // 👈 force it to match existing categories

        ]);

        $post = Post::create([
            'title' => $validated['title'],
            'body'  => $validated['body'],
            'user_id' => auth()->id(),
            'category_id' => $validated['category_id'],
        ]);

        // 🔍 Extract tagged @usernames from body and attach them
        $taggedUserIds = $this->extractTaggedUserIds($validated['body']);
        if (!empty($taggedUserIds)) {
            $post->taggedUsers()->sync($taggedUserIds);
        }

        return new PostResource($post);
    }

    public function show(Post $post)
    {
        $this->authorize('view', $post);
        return new PostResource($post);
    }

    public function update(Request $request, Post $post)
    {
        $this->authorize('update', $post);

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'body'  => 'sometimes|required|string',
            'category_id' => 'required|exists:categories,id', // 👈 force it to match existing categories

        ]);

        $post->update($validated);

        if (isset($validated['body'])) {
            $taggedUserIds = $this->extractTaggedUserIds($validated['body']);
            $post->taggedUsers()->sync($taggedUserIds);
        }

        return new PostResource($post);
    }

    public function destroy(Post $post)
    {
        $this->authorize('delete', $post);
        $post->delete();

        return response()->json(['message' => 'Post soft deleted.']);
    }

    public function trashed()
    {
        $query = Post::onlyTrashed()->get();
    
        //if (!auth()->user()->hasRole('admin')) {
        //    $query->where('user_id', auth()->id());
        //}
        $posts = $query;

        return PostResource::collection($posts);
    }

public function restore($id, Post $post)
{
    $this->authorize('restore', $post);

    $post = Post::onlyTrashed()->findOrFail($id);

    // auth check or policy here...

    $post->restore();

    return response()->json(['message' => 'Post restored successfully']);
}

// ✅ For permanently deleting trashed post
public function forceDelete($id)
{
    $post = Post::onlyTrashed()->findOrFail($id);

    // auth check or policy here...

    $post->forceDelete();

    return response()->json(['message' => 'Post permanently deleted']);
}
    // 🔧 Helper: Extract @usernames from body and return their IDs
    private function extractTaggedUserIds(string $body): array
    {
        preg_match_all('/@([\w]+)/', $body, $matches);
        $usernames = $matches[1] ?? [];

        return User::whereIn('name', $usernames)->pluck('id')->toArray();
    }
}
