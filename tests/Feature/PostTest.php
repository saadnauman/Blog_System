<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
//import Category model
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PostTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create roles and permissions
        $adminRole = Role::create(['name' => 'admin']);
        $userRole = Role::create(['name' => 'user']);

        // You can define granular permissions if needed
        $permissions = ['create posts', 'view posts', 'update posts', 'delete posts'];
        foreach ($permissions as $perm) {
            Permission::create(['name' => $perm]);
        }
        //create category id 1 only for testing
        //the id must be 1 for the test to pass
        //create only 1 category with id =1
        Category::factory()->create(['id' => 1]);

        // Give admin all permissions, user only limited
        $adminRole->givePermissionTo(Permission::all());
        $userRole->givePermissionTo(['create posts', 'view posts', 'update posts']);

        // Create admin & regular user
        $this->admin = User::factory()->create();
        $this->admin->assignRole($adminRole);

        $this->user = User::factory()->create();
        $this->user->assignRole($userRole);
    }

    /** @test */
    public function admin_can_create_post()
    {
        Sanctum::actingAs($this->admin);
        $payload = [
            'title' => 'Admin Post',
            'body' => 'User post content',
            'category_id' => 1,
        ];

        $response = $this->postJson('/api/posts', $payload);

        $response->assertStatus(201);
                 

        $this->assertDatabaseHas('posts', ['title' => 'Admin Post', 'user_id' => $this->admin->id]);
    }


    /** @test */
    public function user_can_create_own_post()
    {
        Sanctum::actingAs($this->user);
        
        $payload = [
            'title' => 'User Post',
            'body' => 'User post content',
            'category_id' => 1, // Assuming category with ID 1 exists

        ];

        $response = $this->postJson('/api/posts', $payload);
        //dd($response->json());
        $response->assertStatus(201);
        $this->assertDatabaseHas('posts', ['title' => 'User Post', 'user_id' => $this->user->id]);
    }
    
    

    /** @test */
    public function user_cannot_update_others_post()
    {
        Sanctum::actingAs($this->user);

        $otherPost = Post::factory()->create(['user_id' => $this->admin->id]);

        $payload = ['title' => 'Hacked Title'];

        $response = $this->putJson("/api/posts/{$otherPost->id}", $payload);

        $response->assertStatus(403);
    }

    /** @test */
    public function admin_can_update_any_post()
    {
        Sanctum::actingAs($this->admin);

        $post = Post::factory()->create(['user_id' => $this->user->id]);

        $payload = ['title' => 'Updated by Admin'];

        $response = $this->putJson("/api/posts/{$post->id}", $payload);

        $response->assertStatus(200);
        $this->assertDatabaseHas('posts', ['title' => 'Updated by Admin']);
    }

    /** @test */
    public function admin_can_delete_any_post()
    {
        Sanctum::actingAs($this->admin);

        $post = Post::factory()->create(['user_id' => $this->user->id]);

        $response = $this->deleteJson("/api/posts/{$post->id}");

        $response->assertStatus(200);
        $this->assertSoftDeleted('posts', ['id' => $post->id]);
    }

    /** @test */
    public function user_can_delete_own_post()
    {
        Sanctum::actingAs($this->user);

        $post = Post::factory()->create(['user_id' => $this->user->id]);

        $response = $this->deleteJson("/api/posts/{$post->id}");

        $response->assertStatus(200);
        $this->assertSoftDeleted('posts', ['id' => $post->id]);
    }
}
