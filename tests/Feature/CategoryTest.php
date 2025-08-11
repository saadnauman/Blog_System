<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $token;

    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('migrate'); // ensure fresh DB for each test
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'user']);
    \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        // Create an admin user and assign role
        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');

        // Login as admin to get token
        $response = $this->postJson('/api/login', [
            'email' => $this->admin->email,
            'password' => 'password', // default factory password
        ]);

        $this->token = $response->json('meta.token');
    }

    /** @test */
    public function admin_can_view_all_categories()
    {
        Category::factory(3)->create();

        $response = $this->getJson('/api/categories', [
            'Authorization' => 'Bearer ' . $this->token
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data' => [
                         '*' => ['id', 'name', 'slug', 'created_at', 'updated_at']
                     ]
                 ]);
    }

    /** @test */
    public function admin_can_create_a_category()
    {
        $response = $this->postJson('/api/categories', [
            'name' => 'Tech'
        ], [
            'Authorization' => 'Bearer ' . $this->token
        ]);

        $response->assertStatus(201)
                 ->assertJsonPath('data.name', 'Tech');

        $this->assertDatabaseHas('categories', ['name' => 'Tech']);
    }

    /** @test */
    public function admin_can_update_a_category()
    {
        $category = Category::factory()->create();

        $response = $this->putJson("/api/categories/{$category->id}", [
            'name' => 'Updated Category'
        ], [
            'Authorization' => 'Bearer ' . $this->token
        ]);

        $response->assertStatus(200)
                 ->assertJsonPath('data.name', 'Updated Category');

        $this->assertDatabaseHas('categories', ['name' => 'Updated Category']);
    }

    /** @test */
    public function admin_can_delete_a_category()
    {
        $category = Category::factory()->create();

        $response = $this->deleteJson("/api/categories/{$category->id}", [], [
            'Authorization' => 'Bearer ' . $this->token
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    /** @test */
    public function non_admin_cannot_manage_categories()
    {
        $user = User::factory()->create(); // No admin role
        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);
        $token = $response->json('meta.token');

        $create = $this->postJson('/api/categories', ['name' => 'Test'], [
            'Authorization' => 'Bearer ' . $token
        ]);

        $create->assertStatus(403);
    }
}
