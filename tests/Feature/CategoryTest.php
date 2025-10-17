<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use App\Models\Category;
use App\Models\Skill;
use App\Models\User;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function can_get_all_categories()
    {
        Category::factory()->count(3)->create();

        $response = $this->getJson('/api/categories');

        $response->assertStatus(200)
                ->assertJsonCount(3)
                ->assertJsonStructure([
                        '*' => [
                            'id',
                            'name',
                            'description',
                            'icon_url'
                        ]
                ]);
    }

    #[Test]
    public function can_get_single_category()
    {
        $category = Category::factory()->create();

        $response = $this->getJson("/api/categories/{$category->id}");

        $response->assertStatus(200)
                ->assertJsonFragment([
                        'id' => $category->id,
                        'name' => $category->name
                ]);
    }

    #[Test]
    public function can_get_skills_by_category()
    {
        $category = Category::factory()->create();
        $skills = Skill::factory()->count(2)->create(['category_id' => $category->id]);

        $response = $this->getJson("/api/categories/{$category->id}");

        $response->assertStatus(200)
                ->assertJsonStructure([
                        'skills' => [
                            '*' => [
                                'id',
                                'category_id'
                            ],
                            '*' => [
                                'id',
                                'category_id'
                            ]
                        ]
                ]);
    }

    #[Test]
    public function admin_can_create_category()
    {
        $admin = User::factory()->create(['user_type' => User::TYPE_ADMIN]);
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->postJson('/api/categories', [
            'name' => 'New Category',
            'description' => 'Category description',
            'icon_url' => 'https://example.com/icon.png'
        ]);

        $response->assertStatus(201)
                ->assertJson([
                        'name' => 'New Category',
                        'description' => 'Category description'
                ]);

        $this->assertDatabaseHas('categories', [
            'name' => 'New Category'
        ]);
    }

    #[Test]
    public function non_admin_cannot_create_category()
    {
        $user = User::factory()->create(['user_type' => User::TYPE_CLIENT]);
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->postJson('/api/categories', [
            'name' => 'New Category',
            'description' => 'Category description'
        ]);        

        $response->assertStatus(403);
    }

    #[Test]
    public function admin_can_update_category()
    {
        $admin = User::factory()->create(['user_type' => User::TYPE_ADMIN]);
        $token = $admin->createToken('test-token')->plainTextToken;
        $category = Category::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->putJson("/api/categories/{$category->id}", [
            'name' => 'Updated Category',
            'description' => 'Updated description'
        ]);

        $response->assertStatus(200)
                ->assertJson([
                        'name' => 'Updated Category',
                        'description' => 'Updated description'
                ]);

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Updated Category'
        ]);
    }

    #[Test]
    public function admin_can_delete_category()
    {
        $admin = User::factory()->create(['user_type' => User::TYPE_ADMIN]);
        $token = $admin->createToken('test-token')->plainTextToken;
        $category = Category::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->deleteJson("/api/categories/{$category->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('categories', [
            'id' => $category->id
        ]);
    }

    #[Test]
    public function can_get_categories_with_skills()
    {
        $category = Category::factory()->create();
        Skill::factory()->count(2)->create(['category_id' => $category->id]);

        // This endpoint should be accessible without authentication since it's for public use
        $response = $this->getJson('/api/categories/with-skills');

        $response->assertStatus(200)
                ->assertJsonStructure([
                        '*' => [
                            'id',
                            'name',
                            'skills' => [
                                '*' => [
                                    'id',
                                    'name'
                                ]
                            ]
                        ]
                ]);
    }
}
