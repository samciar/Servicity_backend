<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use App\Models\Bid;
use App\Models\Task;
use App\Models\User;
use App\Models\Category;

class BidTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function tasker_can_create_bid()
    {
        $tasker = User::factory()->create(['user_type' => User::TYPE_TASKER]);
        $client = User::factory()->create(['user_type' => User::TYPE_CLIENT]);
        $category = Category::factory()->create();
        $task = Task::factory()->create([
            'client_id' => $client->id,
            'category_id' => $category->id
        ]);
        
        $token = $tasker->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->postJson('/api/bids', [
            'task_id' => $task->id,
            'bid_amount' => 80.00,
            'message' => 'I can complete this task efficiently',
            'estimated_hours' => 4
        ]);

        $response->assertStatus(201)
                ->assertJson([
                        'bid_amount' => 80.00,
                        'message' => 'I can complete this task efficiently',
                        'status' => 'pending'
                ]);

        $this->assertDatabaseHas('bids', [
            'task_id' => $task->id,
            'tasker_id' => $tasker->id,
            'bid_amount' => 80.00
        ]);
    }

    #[Test]
    public function can_get_all_bids()
    {
        // Create required entities first
        $tasker1 = User::factory()->create(['user_type' => User::TYPE_TASKER]);
        $tasker2 = User::factory()->create(['user_type' => User::TYPE_TASKER]);
        $tasker3 = User::factory()->create(['user_type' => User::TYPE_TASKER]);
        
        $client = User::factory()->create(['user_type' => User::TYPE_CLIENT]);
        $category = Category::factory()->create();
        
        $task1 = Task::factory()->create([
            'client_id' => $client->id,
            'category_id' => $category->id
        ]);
        $task2 = Task::factory()->create([
            'client_id' => $client->id,
            'category_id' => $category->id
        ]);
        $task3 = Task::factory()->create([
            'client_id' => $client->id,
            'category_id' => $category->id
        ]);

        // Create bids with explicit relationships
        Bid::factory()->create([
            'task_id' => $task1->id,
            'tasker_id' => $tasker1->id
        ]);
        Bid::factory()->create([
            'task_id' => $task2->id,
            'tasker_id' => $tasker2->id
        ]);
        Bid::factory()->create([
            'task_id' => $task3->id,
            'tasker_id' => $tasker3->id
        ]);

        // Use the client user to get bids (since index method only returns bids for client's tasks)
        $token = $client->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->getJson('/api/bids');

        $response->assertStatus(200)
                ->assertJsonCount(3)
                ->assertJsonStructure([
                        '*' => [
                            'id',
                            'bid_amount',
                            'message',
                            'status'
                        ]
                ]);
    }

    #[Test]
    public function can_get_single_bid()
    {
        $bid = Bid::factory()->create();
        // Use the client who owns the task to view the bid (per policy)
        $client = User::find($bid->task->client_id);
        $token = $client->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->getJson("/api/bids/{$bid->id}");

        $response->assertStatus(200)
                ->assertJson([
                        'id' => $bid->id,
                        'bid_amount' => $bid->bid_amount
                ]);
    }

    #[Test]
    public function tasker_can_update_own_bid()
    {
        $tasker = User::factory()->create(['user_type' => User::TYPE_TASKER]);
        $bid = Bid::factory()->create(['tasker_id' => $tasker->id]);
        $token = $tasker->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->putJson("/api/bids/{$bid->id}", [
            'bid_amount' => 90.00,
            'message' => 'Updated proposal'
        ]);

        $response->assertStatus(200)
                ->assertJson([
                        'bid_amount' => 90.00,
                        'message' => 'Updated proposal'
                ]);

        $this->assertDatabaseHas('bids', [
            'id' => $bid->id,
            'bid_amount' => 90.00
        ]);
    }

    #[Test]
    public function tasker_can_withdraw_own_bid()
    {
        $tasker = User::factory()->create(['user_type' => User::TYPE_TASKER]);
        $bid = Bid::factory()->create(['tasker_id' => $tasker->id]);
        $token = $tasker->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->postJson("/api/bids/{$bid->id}/withdraw");

        $response->assertStatus(200)
                ->assertJson([
                    'message' => 'Bid withdrawn successfully'
                ]);

        $this->assertDatabaseHas('bids', [
            'id' => $bid->id,
            'status' => 'withdrawn'
        ]);
    }

    #[Test]
    public function client_can_accept_bid()
    {
        $client = User::factory()->create(['user_type' => User::TYPE_CLIENT]);
        $task = Task::factory()->create(['client_id' => $client->id]);
        $bid = Bid::factory()->create(['task_id' => $task->id]);
        $token = $client->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->postJson("/api/bids/{$bid->id}/accept");

        $response->assertStatus(200)
                ->assertJson([
                    'message' => 'Bid accepted successfully'
                ]);

        $this->assertDatabaseHas('bids', [
            'id' => $bid->id,
            'status' => 'accepted'
        ]);
    }

    #[Test]
    public function client_can_reject_bid()
    {
        $client = User::factory()->create(['user_type' => User::TYPE_CLIENT]);
        $task = Task::factory()->create(['client_id' => $client->id]);
        $bid = Bid::factory()->create(['task_id' => $task->id]);
        $token = $client->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->postJson("/api/bids/{$bid->id}/reject");

        $response->assertStatus(200)
                ->assertJson([
                    'message' => 'Bid rejected successfully'
                ]);

        $this->assertDatabaseHas('bids', [
            'id' => $bid->id,
            'status' => 'rejected'
        ]);
    }

    #[Test]
    public function can_get_pending_bids()
    {
        $client = User::factory()->create(['user_type' => User::TYPE_CLIENT]);
        $task = Task::factory()->create(['client_id' => $client->id]);
        
        Bid::factory()->create(['status' => 'pending', 'task_id' => $task->id]);
        Bid::factory()->create(['status' => 'accepted', 'task_id' => $task->id]);
        
        $token = $client->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->getJson('/api/bids/pending');

        $response->assertStatus(200)
                ->assertJsonCount(1);
    }

    #[Test]
    public function can_get_accepted_bids()
    {
        $tasker = User::factory()->create(['user_type' => User::TYPE_TASKER]);
        
        Bid::factory()->create(['status' => 'accepted', 'tasker_id' => $tasker->id]);
        Bid::factory()->create(['status' => 'pending', 'tasker_id' => $tasker->id]);
        
        $token = $tasker->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->getJson('/api/bids/accepted');

        $response->assertStatus(200)
                ->assertJsonCount(1);
    }

    #[Test]
    public function non_tasker_cannot_create_bid()
    {
        $client = User::factory()->create(['user_type' => User::TYPE_CLIENT]);
        $task = Task::factory()->create();
        $token = $client->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->postJson('/api/bids', [
            'task_id' => $task->id,
            'bid_amount' => 80.00,
            'message' => 'Test proposal'
        ]);

        $response->assertStatus(403);
    }

    #[Test]
    public function tasker_cannot_bid_on_own_task()
    {
        $tasker = User::factory()->create(['user_type' => User::TYPE_TASKER]);
        $task = Task::factory()->create(['client_id' => $tasker->id]);
        $token = $tasker->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->postJson('/api/bids', [
            'task_id' => $task->id,
            'bid_amount' => 80.00,
            'message' => 'Test proposal'
        ]);

        $response->assertStatus(403);
    }
}
