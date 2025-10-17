<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use App\Models\Task;
use App\Models\User;
use App\Models\Category;
use App\Models\Skill;
use Carbon\Carbon;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function client_can_create_task()
    {
        $client = User::factory()->create(['user_type' => User::TYPE_CLIENT]);
        $category = Category::factory()->create();
        $skill = Skill::factory()->create(['category_id' => $category->id]);
        
        $token = $client->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->postJson('/api/tasks', [
            'title' => 'Test Task',
            'description' => 'Task description',
            'category_id' => $category->id,
            'budget_type' => 'fixed',
            'budget_amount' => 100.00,
            'preferred_date' => now()->addDays(7)->toDateString(),
            'preferred_time' => '10:00',
            'location' => 'Test Location',
            'latitude' => 4.6097,
            'longitude' => -74.0817,
            'skill_ids' => [$skill->id]
        ]);

        $response->assertStatus(201)
                ->assertJsonFragment([
                    'title' => 'Test Task',
                    'description' => 'Task description'
                ]);

        $this->assertDatabaseHas('tasks', [
            'title' => 'Test Task',
            'client_id' => $client->id
        ]);
    }

    #[Test]
    public function can_get_all_tasks()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        Task::factory()->count(3)->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->getJson('/api/tasks');

        $response->assertStatus(200)
                ->assertJsonCount(3)
                ->assertJsonStructure([
                        '*' => [
                            'id',
                            'title',
                            'description',
                            'budget_amount',
                            'status'
                        ]
                ]);
    }

    #[Test]
    public function can_get_single_task()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;
        $task = Task::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->getJson("/api/tasks/{$task->id}");

        $response->assertStatus(200)
                ->assertJsonFragment([
                    'id' => $task->id,
                    'title' => $task->title
                ]);
    }

    #[Test]
    public function client_can_update_own_task()
    {
        $client = User::factory()->create(['user_type' => User::TYPE_CLIENT]);
        $task = Task::factory()->create(['client_id' => $client->id]);
        $token = $client->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->putJson("/api/tasks/{$task->id}", [
            'title' => 'Updated Task',
            'description' => 'Updated description',
            'budget_type' => 'fixed',
            'budget_amount' => 150.00
        ]);

        $response->assertStatus(200)
                ->assertJsonFragment([
                    'title' => 'Updated Task'
                ]);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Updated Task'
        ]);
    }

    #[Test]
    public function client_can_delete_own_task()
    {
        $client = User::factory()->create(['user_type' => User::TYPE_CLIENT]);
        $task = Task::factory()->create(['client_id' => $client->id]);
        $token = $client->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->deleteJson("/api/tasks/{$task->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('tasks', [
            'id' => $task->id
        ]);
    }

    #[Test]
    public function client_can_get_own_tasks()
    {
        $client = User::factory()->create(['user_type' => User::TYPE_CLIENT]);
        Task::factory()->count(2)->create(['client_id' => $client->id]);
        Task::factory()->create(); // Another client's task
        
        $token = $client->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->getJson('/api/user/tasks');

        $response->assertStatus(200)
                ->assertJsonCount(2);
    }

    #[Test]
    public function can_search_tasks()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        Task::factory()->create(['title' => 'Plumbing task']);
        Task::factory()->create(['title' => 'Electrical work']);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->getJson('/api/tasks/search?query=plumbing');

        $response->assertStatus(200)
                ->assertJsonFragment(['title' => 'Plumbing task']);
    }

    #[Test]
    public function can_get_urgent_tasks()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        Task::factory()->create(['deadline_at' => Carbon::now()->toDateString()]);
        Task::factory()->create(['deadline_at' => Carbon::now()->addDays(3)->toDateString()]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->getJson('/api/tasks/urgent');        

        $response->assertStatus(200)
                ->assertJsonCount(1);
    }

    #[Test]
    public function client_can_update_task_status()
    {
        $client = User::factory()->create(['user_type' => User::TYPE_CLIENT]);
        $task = Task::factory()->create(['client_id' => $client->id]);
        $token = $client->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->putJson("/api/tasks/{$task->id}/status", [
            'status' => 'completed'
        ]);

        $response->assertStatus(200)
                ->assertJsonFragment([
                    'status' => 'completed'
                ]);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'status' => 'completed'
        ]);
    }

    #[Test]
    public function non_client_cannot_create_task()
    {
        $tasker = User::factory()->create(['user_type' => User::TYPE_TASKER]);
        $token = $tasker->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->postJson('/api/tasks', [
            'title' => 'Test Task',
            'description' => 'Task description'
        ]);

        $response->assertStatus(403);
    }
}
