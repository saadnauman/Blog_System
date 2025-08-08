<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

/**
 * @mixin \Spatie\Permission\Traits\HasRoles
 * 
 * @method bool hasRole(string|array|\Spatie\Permission\Models\Role $roles, string|null $guard = null)
 * @method bool assignRole(...$roles)
 * @method bool removeRole(string|\Spatie\Permission\Models\Role $role)
 * @method bool hasPermissionTo(string|\Spatie\Permission\Models\Permission $permission, string|null $guard = null)
 * @method bool can(string $permission)
 * @method \Illuminate\Database\Eloquent\Collection getRoleNames()
 * @method \Illuminate\Database\Eloquent\Collection getPermissionNames()
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role', // admin or user
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Get the user's role name
     */
    public function getRole(): string
    {
        return $this->getRoleNames()->first() ?? 'no-role';
    }

    /**
     * Get the posts for the user.
     */
    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    /**
     * Get posts where user is tagged
     */
    public function taggedInPosts()
    {
        return $this->belongsToMany(Post::class, 'post_user_tags');
    }
}