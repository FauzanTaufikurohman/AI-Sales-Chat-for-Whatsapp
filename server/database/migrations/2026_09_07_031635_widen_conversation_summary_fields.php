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
        Schema::table('conversation_summaries', function (Blueprint $table) {
            $table->text('summary')->change();
            $table->string('intent', 255)->nullable()->change();
            $table->string('lead_status', 255)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('conversation_summaries', function (Blueprint $table) {
            $table->string('summary', 255)->change();
            $table->string('intent', 50)->nullable()->change();
            $table->string('lead_status', 30)->nullable()->change();
        });
    }
};
