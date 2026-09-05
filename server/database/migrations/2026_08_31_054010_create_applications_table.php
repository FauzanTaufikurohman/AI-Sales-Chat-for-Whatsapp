<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->string('email', 120)->unique();
            $table->string('phone_number', 20);
            $table->string('phone_verified_at', 20)->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('otp_verifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->onDelete('cascade');
            $table->string('phone_number', 20);
            $table->string('purpose', 30);
            $table->string('otp_hash');
            $table->timestamp('expires_at');
            $table->unsignedBigInteger('attempts')->default(0);
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
            $table->index(['phone_number', 'purpose']);
        });

        Schema::create('whatsapp_bots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();
            $table->string('phone_number', 20)->unique();
            $table->string('provider')->nullable();
            $table->string('provider_phone_id')->nullable();
            $table->string('provider_account_id')->nullable();
            $table->string('provider_token')->nullable();
            $table->string('name', 120)->nullable();
            $table->text('system_prompt')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index(['user_id']);
        });

        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bot_id')
                ->constrained('whatsapp_bots')
                ->cascadeOnDelete();
            $table->string('customer_phone', 30);
            $table->string('customer_name', 120)->nullable();
            $table->string('status', 30)->default('active');
            $table->timestamps();
            $table->timestamp('last_message_at')->nullable();
            $table->index([
                'bot_id', 'customer_phone',
            ]);
            $table->index('last_message_at');
        });

        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')
                ->constrained('conversations')
                ->cascadeOnDelete();
            $table->string('sender_type', 30);
            $table->text('message');
            $table->string('message_type', 30)->default('text');
            $table->string('provider_message_id', 120)->nullable()->index();
            $table->boolean('is_ai_processed')->default(false);
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
            $table->index(['conversation_id', 'created_at']);
        });

        Schema::create('conversation_summaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')
                ->constrained('conversations')
                ->cascadeOnDelete();
            $table->text('summary');
            $table->json('important_information')->nullable();
            $table->string('intent', 50)->nullable();
            $table->boolean('purchase_intent')->default(false);
            $table->string('lead_status', 30)->nullable();
            $table->timestamp('generated_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conversation_summaries');
        Schema::dropIfExists('messages');
        Schema::dropIfExists('conversations');
        Schema::dropIfExists('whatsapp_bots');
        Schema::dropIfExists('otp_verifications');
        Schema::dropIfExists('users');
    }
};
