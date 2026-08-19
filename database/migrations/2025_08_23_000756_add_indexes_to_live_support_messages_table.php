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
        Schema::table('live_support_messages', function (Blueprint $table) {
            // Composite index for conversation queries (topic_id + user_id)
            $table->index(['topic_id', 'user_id'], 'live_support_conversation_index');

            // Index for user-specific queries
            $table->index('user_id', 'live_support_user_index');

            // Index for topic-specific queries
            $table->index('topic_id', 'live_support_topic_index');

            // Index for agent queries
            $table->index('agent_id', 'live_support_agent_index');

            // Index for timestamp ordering
            $table->index('created_at', 'live_support_created_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('live_support_messages', function (Blueprint $table) {
            $table->dropIndex('live_support_conversation_index');
            $table->dropIndex('live_support_user_index');
            $table->dropIndex('live_support_topic_index');
            $table->dropIndex('live_support_agent_index');
            $table->dropIndex('live_support_created_index');
        });
    }
};
