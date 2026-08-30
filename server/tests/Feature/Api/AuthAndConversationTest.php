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

    public function test_user_must_verify_phone_before_login_after_register(): void
    {
        $registration = $this->postJson('/api/auth/register', [
            'name' => 'Raka Sales',
            'email' => 'raka@example.com',
            'phone_number' => '+628120000000',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $registration->assertCreated()
            ->assertJsonPath('message', 'Registration successful. OTP sent for phone verification.')
            ->assertJsonPath('user.status', 'pending');

        $otp = $registration->json('otp');
        $this->assertNotEmpty($otp);
        $this->assertMatchesRegularExpression('/^\d{6}$/', (string) $otp);

        $this->postJson('/api/auth/login', [
            'login' => 'raka@example.com',
            'password' => 'Password123!',
        ])->assertForbidden();

        $this->postJson('/api/auth/verify-otp', [
            'phone_number' => '+628120000000',
            'otp' => $otp,
        ])->assertOk()->assertJsonPath('message', 'OTP verified successfully.');

        $this->postJson('/api/auth/login', [
            'login' => 'raka@example.com',
            'password' => 'Password123!',
        ])->assertOk()->assertJsonStructure(['user', 'token']);
    }

    public function test_user_can_request_and_verify_phone_otp(): void
    {
        $phoneNumber = '+628123456789';

        $sendOtpResponse = $this->postJson('/api/auth/send-otp', [
            'phone_number' => $phoneNumber,
        ]);

        $sendOtpResponse->assertOk()->assertJsonPath('message', 'OTP sent successfully.');

        $otp = $sendOtpResponse->json('otp');
        $this->assertNotEmpty($otp);
        $this->assertMatchesRegularExpression('/^\d{6}$/', (string) $otp);

        $this->postJson('/api/auth/verify-otp', [
            'phone_number' => $phoneNumber,
            'otp' => $otp,
        ])->assertOk()->assertJsonPath('message', 'OTP verified successfully.');
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
