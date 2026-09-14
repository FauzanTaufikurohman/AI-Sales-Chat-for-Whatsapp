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
        Schema::table('whatsapp_bots', function (Blueprint $table) {
            $table->string('fonnte_device')->nullable()->unique();
            $table->text('fonnte_token')->nullable();
            $table->string('connection_status')->default('disconnected');
            $table->timestamp('connected_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('whatsapp_bots', function (Blueprint $table) {
            $table->dropColumn([
                'fonnte_device',
                'fonnte_token',
                'connection_status',
                'connected_at',
            ]);
        });
    }
};
