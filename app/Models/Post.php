<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;

class Post extends Model
{
    use HasFactory, SoftDeletes, Searchable;

    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'body',
        'is_published',
    ];

    protected $casts = [
        'is_published' => 'boolean',
    ];

    /**
     * Get the user that owns the post.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    //use Searchable;

    // ...

    

    /**
     * Get the category that owns the post.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function toSearchableArray()
    {
        $array = $this->toArray();
        $array['author'] = $this->user ? $this->user->name : '';
        return [
            'title' => $this->title,
            'body' => $this->body,
            'author' => $array['author'],
        ];
    }
    //to get tagged users
    public function taggedUsers()
{
    return $this->belongsToMany(User::class, 'post_user_tags');
}
} 