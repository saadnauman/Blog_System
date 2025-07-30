<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{

public function index(Request $request)
{
    $authors = \App\Models\User::orderBy('name')->get();
    $categories = \App\Models\Category::orderBy('name')->get();

    // If searching, use Scout
    if ($request->filled('search')) {
        $searchQuery = $request->input('search');
        $posts = Post::search($searchQuery)
            ->get()
            ->load('user', 'category');

        // Optional: filter search results by author/category/date
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

        // Paginate manually since Scout returns a Collection
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
        // No search: use Eloquent builder for filters
        $query = Post::with('user', 'category');
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
        $posts = $query->latest()->paginate(10);
        $posts->appends($request->query());
    }

    return view('pages.posts.index', compact('posts', 'authors', 'categories'));
}
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
        $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'body' => 'required',
        ]);
        $post = Post::create([
            'user_id' => Auth::id(),
            'category_id' => $request->category_id,
            'title' => $request->title,
            'body' => $request->body,
            'is_published' => $request->has('is_published'),
        ]);
        return redirect()->route('posts.show', $post)->with('status', 'Post created!');
    }

    public function edit(Post $post)
    {
        $this->authorizePost($post);
        $categories = Category::orderBy('name')->get();
        return view('pages.posts.form', [
            'categories' => $categories,
            'edit' => true,
            'post' => $post,
            'isAdmin' => Auth::user()->role === 'admin',
        ]);
    }

    public function update(Request $request, Post $post)
    {
        $this->authorizePost($post);
        $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'body' => 'required',
        ]);
        $post->update([
            'category_id' => $request->category_id,
            'title' => $request->title,
            'body' => $request->body,
            'is_published' => $request->has('is_published'),
        ]);
        return redirect()->route('posts.show', $post)->with('status', 'Post updated!');
    }

    public function destroy(Post $post)
    {
        $this->authorizePost($post);
        $post->delete();
        return redirect()->route('posts.index')->with('status', 'Post deleted!');
    }

    private function authorizePost(Post $post)
    {
        if (Auth::user()->role !== 'admin' && $post->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
    }
    public function userPosts()
    {
      $posts = Post::with('category')
          ->where('user_id', Auth::id())
          ->latest()
          ->paginate(10);

        return view('pages.posts.user_posts', compact('posts'));
    }
} 