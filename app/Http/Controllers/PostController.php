<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    // Authorize actions on a post
    private function authorizePost(Post $post)
    {
        if (Auth::user()->role !== 'admin' && $post->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
    }
    // Manage posts (admin or own posts)
public function manage()
{
    $user = Auth::user();
    $isAdmin = $user->role === 'admin';

    $query = Post::withTrashed()->with('user', 'category');

    if (!$isAdmin) {
        $query->where('user_id', $user->id); // Only own posts
    }

    $posts = $query->latest()->paginate(10);

    return view('pages.posts.manage', compact('posts'));
}

    // List all posts with filters and search
    // ✅ Use Scout for search, Eloquent for filters
    // ✅ Use SoftDeletes to show deleted posts for admins
    // ✅ Use pagination
    // ✅ Use Blade components for UI
    // ✅ Use @can directive for actions
    // ✅ Use @auth directive for showing actions
    // ✅ Use @if directive for showing actions based on role
    // ✅ Use @foreach directive for looping through posts
    // ✅ Use @include directive for including components
    // ✅ Use @section and @yield for layout
    // ✅ Use @csrf directive for forms
public function index(Request $request, Post $post)
{
    $authors = \App\Models\User::orderBy('name')->get();
    $categories = \App\Models\Category::orderBy('name')->get();
    $isAdmin = Auth::user()->role === 'admin' ;
    // ✅ Start the query based on role
    if ($isAdmin) {
        $query = Post::withTrashed()->with('user', 'category');
    } else {
        // Show all NON-deleted posts, not just user's
        $query = Post::with('user', 'category')->whereNull('deleted_at');
    }
    // if same user made that post, show all of his posts even if soft deleted

    // ✅ Apply filters (author, category, date)
    if ($request->filled('author')) {
        $query->where('user_id', $request->author);
    }
    if ($request->filled('category')) {
        $query->where('category_id', $request->category);
    }
    if ($request->created_at === 'today') {
        $query->whereDate('created_at', now()->toDateString());
    } elseif ($request->created_at === 'week') {
        $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
    }

    // ✅ If searching, use Scout
    if ($request->filled('search')) {
        $searchQuery = $request->input('search');
        $posts = Post::search($searchQuery)->get()->load('user', 'category');

        // Manually apply filters to collection
        if (!$isAdmin) {
            $posts = $posts->reject(fn ($post) => $post->deleted_at !== null);
        }
        if ($request->filled('author')) {
            $posts = $posts->where('user_id', $request->author);
        }
        if ($request->filled('category')) {
            $posts = $posts->where('category_id', $request->category);
        }
        if ($request->created_at === 'today') {
            $posts = $posts->where('created_at', '>=', now()->startOfDay());
        } elseif ($request->created_at === 'week') {
            $posts = $posts->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
        }

        // Manual pagination for collections
        $page = $request->input('page', 1);
        $perPage = 10;
        $pagedPosts = $posts->forPage($page, $perPage);
        $posts = new \Illuminate\Pagination\LengthAwarePaginator(
            $pagedPosts,
            $posts->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );
    } else {
        // Standard pagination
        $posts = $query->latest()->paginate(10);
        $posts->appends($request->query());
    }

    return view('pages.posts.index', compact('posts', 'authors', 'categories'));
}

//suggestions through mailable based on search query
public function suggestions(Request $request)
{
    $query = $request->input('q');
    $results = [];
    if ($query) {
        $results = Post::search($query)
            ->take(5)
            ->get()
            ->map(function($post) {
                return [
                    'id' => $post->id,
                    'title' => $post->title,
                ];
            });
    }
    return response()->json($results);
}

    public function show(Post $post)
    {
        return view('pages.posts.show', compact('post'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        return view('pages.posts.form', [
            'categories' => $categories,
            'edit' => false,
            'post' => null,
            'isAdmin' => Auth::user()->role === 'admin',
        ]);
    }

  public function store(Request $request)
{
    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'category_id' => 'required|exists:categories,id',
        'body' => 'required',
        'tagged_user_ids' => 'nullable|array',
        'tagged_user_ids.*' => 'exists:users,id',
    ]);

    $post = Post::create([
        'user_id' => Auth::id(),
        'category_id' => $validated['category_id'],
        'title' => $validated['title'],
        'body' => $validated['body'],
        'is_published' => $request->has('is_published'),
    ]);

    // Extract @mentions from body
    preg_match_all('/@([\w.]+)/', $validated['body'], $matches);
    //dd($matches);

    $mentionedUsernames = $matches[1] ?? [];

    $mentionedUserIds = \App\Models\User::whereIn('name', $mentionedUsernames)->pluck('id')->toArray();
    //dd($mentionedUserIds);
    // Merge manually tagged users if any
    $manualTags = $validated['tagged_user_ids'] ?? [];
    $allTaggedUserIds = array_unique(array_merge($manualTags, $mentionedUserIds));
    //dd($allTaggedUserIds);
    $post->taggedUsers()->sync($allTaggedUserIds);
   // ✅ Fetch the actual User models for notification
$allTaggedUsers = \App\Models\User::whereIn('id', $allTaggedUserIds)->get();
$post = Post::with('user')->find($post->id); // make sure the post has user loaded



// Notify each user
foreach ($allTaggedUsers as $user) {
    $user->notify(new \App\Notifications\UserTaggedInPost($post));
}
    return redirect()->route('posts.show', $post)->with('status', 'Post created!');
}


    public function edit(Post $post)
{
    $this->authorizePost($post);

    $categories = Category::orderBy('name')->get();

    // Get IDs of already tagged users
    $taggedUserIds = $post->taggedUsers()->pluck('users.id')->toArray();

    return view('pages.posts.form', [
        'categories' => $categories,
        'edit' => true,
        'post' => $post,
        'taggedUserIds' => $taggedUserIds, // ✅ Pass to Blade
        'isAdmin' => Auth::user()->role === 'admin',
    ]);
}


    public function update(Request $request, Post $post)
{
    $this->authorizePost($post);

    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'category_id' => 'required|exists:categories,id',
        'body' => 'required',
        'tagged_user_ids' => 'nullable|array',
        'tagged_user_ids.*' => 'exists:users,id',
    ]);

    $post->update([
        'category_id' => $validated['category_id'],
        'title' => $validated['title'],
        'body' => $validated['body'],
        'is_published' => $request->has('is_published'),
    ]);

    // Extract @mentions from body
    preg_match_all('/@([\w.]+)/', $validated['body'], $matches);
    $mentionedUsernames = $matches[1] ?? [];

    $mentionedUserIds = \App\Models\User::whereIn('name', $mentionedUsernames)->pluck('id')->toArray();

    // Merge manually tagged users if any
    $manualTags = $validated['tagged_user_ids'] ?? [];
    $allTaggedUserIds = array_unique(array_merge($manualTags, $mentionedUserIds));
    //dd($allTaggedUserIds);
    $post->taggedUsers()->sync($allTaggedUserIds);
   // ✅ Fetch the actual User models for notification
    $allTaggedUsers = \App\Models\User::whereIn('id', $allTaggedUserIds)->get();
    $post = Post::with('user')->find($post->id); // make sure the post has user loaded



// Notify each user
   foreach ($allTaggedUsers as $user) {
      $user->notify(new \App\Notifications\UserTaggedInPost($post));
    }
    return redirect()->route('posts.show', $post)->with('status', 'Post updated!');

   }

    public function destroy(Post $post)
    {
        $this->authorizePost($post);
        $post->delete();
        return redirect()->route('posts.index')->with('status', 'Post deleted!');
    }

    
    public function userPosts()
    {
      $posts = Post::with('category')
          ->where('user_id', Auth::id())
          ->latest()
          ->paginate(10);

        return view('pages.posts.user_posts', compact('posts'));
    }
    public function hide(Post $post)
{
    try {
        // Check if the authenticated user is authorized to hide this post
        $this->authorize('hide', $post);

        // Perform a soft delete (hide the post)
        $post->delete();

        // Redirect back with success message
        return back()->with('status', 'Post hidden successfully!');

    } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
        // If the user is not authorized, redirect back with error message
        return back()->with('error', 'You are not authorized to hide this post.');
    }
}


public function restore($id)
{
    $post = Post::withTrashed()->findOrFail($id);

    // Optional: Authorize only admin
    if (Auth::user()->role !== 'admin') {
        abort(403);
    }

    $post->restore();

    return back()->with('status', 'Post restored successfully!');
}

} 