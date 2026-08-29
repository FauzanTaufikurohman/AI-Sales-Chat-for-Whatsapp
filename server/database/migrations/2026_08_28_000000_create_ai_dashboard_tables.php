<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->string('phone_number')->nullable()->unique()->after('email');
            $table->timestamp('phone_verified_at')->nullable()->after('email_verified_at');
            $table->string('status')->default('active')->after('phone_verified_at');
        });

        Schema::create('roles', function (Blueprint $table): void {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        Schema::create('user_roles', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
            $table->unique(['user_id', 'role_id']);
        });

        Schema::create('whatsapp_accounts', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('phone_number')->unique();
            $table->string('phone_number_id')->nullable()->unique();
            $table->string('business_account_id')->nullable();
            $table->text('access_token_encrypted')->nullable();
            $table->string('display_name')->nullable();
            $table->string('status')->default('inactive');
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
            $table->index('user_id');
        });

        Schema::create('customers', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('whatsapp_account_id')->constrained()->cascadeOnDelete();
            $table->string('phone_number');
            $table->string('name')->nullable();
            $table->string('profile_picture')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
            $table->unique(['whatsapp_account_id', 'phone_number']);
            $table->index('phone_number');
        });

        Schema::create('conversations', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('whatsapp_account_id')->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('assigned_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status')->default('active');
            $table->string('priority')->default('normal');
            $table->string('ai_status')->default('pending');
            $table->text('ai_summary')->nullable();
            $table->string('ai_intent')->nullable();
            $table->string('ai_sentiment')->nullable();
            $table->text('ai_required_action')->nullable();
            $table->timestamp('last_message_at')->nullable();
            $table->timestamps();
            $table->index(['assigned_user_id', 'status']);
            $table->index(['status', 'last_message_at']);
        });

        Schema::create('messages', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('conversation_id')->constrained()->cascadeOnDelete();
            $table->string('external_message_id')->nullable()->unique();
            $table->string('sender_type');
            $table->string('message_type')->default('text');
            $table->text('content')->nullable();
            $table->string('media_url')->nullable();
            $table->string('mime_type')->nullable();
            $table->boolean('ai_processed')->default(false);
            $table->text('ai_summary')->nullable();
            $table->timestamps();
            $table->index(['conversation_id', 'created_at']);
        });

        Schema::create('ai_summaries', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('conversation_id')->constrained()->cascadeOnDelete();
            $table->text('summary');
            $table->text('customer_requirement')->nullable();
            $table->string('customer_intent')->nullable();
            $table->json('important_information')->nullable();
            $table->json('customer_questions')->nullable();
            $table->text('recommended_action')->nullable();
            $table->string('urgency')->default('normal');
            $table->decimal('confidence', 4, 3)->nullable();
            $table->timestamps();
            $table->index('conversation_id');
        });

        Schema::create('otp_verifications', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('phone_number');
            $table->string('otp_hash');
            $table->timestamp('expires_at');
            $table->unsignedTinyInteger('attempts')->default(0);
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
            $table->index(['phone_number', 'expires_at']);
        });

        Schema::create('notifications', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type');
            $table->string('title');
            $table->text('body');
            $table->json('data')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            $table->index(['user_id', 'read_at']);
        });

        Schema::create('audit_logs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action');
            $table->string('entity_type')->nullable();
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->json('metadata')->nullable();
            $table->ipAddress('ip_address')->nullable();
            $table->timestamps();
            $table->index(['entity_type', 'entity_id']);
        });

        Schema::create('follow_up_reminders', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('conversation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('assigned_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('note')->nullable();
            $table->timestamp('remind_at');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->index(['assigned_user_id', 'remind_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('follow_up_reminders');
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('otp_verifications');
        Schema::dropIfExists('ai_summaries');
        Schema::dropIfExists('messages');
        Schema::dropIfExists('conversations');
        Schema::dropIfExists('customers');
        Schema::dropIfExists('whatsapp_accounts');
        Schema::dropIfExists('user_roles');
        Schema::dropIfExists('roles');
        Schema::table('users', function (Blueprint $table): void {
            $table->dropUnique(['phone_number']);
            $table->dropColumn(['phone_number', 'phone_verified_at', 'status']);
        });
    }
};
