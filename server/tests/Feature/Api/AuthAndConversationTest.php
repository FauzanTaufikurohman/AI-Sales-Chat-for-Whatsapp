<?php

namespace Tests\Feature\Api;

use App\Models\Conversation;
use App\Models\User;
use App\Models\WhatsAppAccount;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthAndConversationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_and_login(): void
    {
        $registration = $this->postJson('/api/auth/register', [
            'name' => 'Raka Sales',
            'email' => 'raka@example.com',
            'phone_number' => '+628120000000',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $registration->assertCreated()->assertJsonStructure(['user', 'token']);

        $this->postJson('/api/auth/login', [
            'login' => 'raka@example.com',
            'password' => 'Password123!',
        ])->assertOk()->assertJsonStructure(['user', 'token']);
    }

    public function test_user_cannot_read_another_users_conversation(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $account = WhatsAppAccount::create([
            'uuid' => fake()->uuid(),
            'user_id' => $owner->id,
            'phone_number' => '+628120000001',
            'status' => 'active',
        ]);
        $conversation = Conversation::create([
            'uuid' => fake()->uuid(),
            'whatsapp_account_id' => $account->id,
            'customer_id' => $account->customers()->create(['uuid' => fake()->uuid(), 'phone_number' => '+628120000002'])->id,
            'status' => 'active',
            'priority' => 'normal',
        ]);

        $this->actingAs($otherUser)->getJson('/api/conversations/'.$conversation->id)->assertForbidden();
    }
}
