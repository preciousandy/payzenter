<?php

use App\Models\ErrorLog;
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
        if (Schema::hasTable('error_logs')) {
            return;
        }

        Schema::create('error_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('error_source');
            $table->text('error_message');
            $table->text('error_details')->nullable(); // Stack trace, additional info
            $table->string('error_code')->nullable();
            $table->string('status')->default(ErrorLog::STATUS_OPEN);
            $table->text('admin_notes')->nullable(); // Admin comments when resolving
            $table->string('resolved_by')->nullable(); // Admin who resolved the error
            $table->timestamp('resolved_at')->nullable();
            $table->string('severity')->default('medium'); // low, medium, high, critical
            $table->string('file_path')->nullable(); // File where error occurred
            $table->integer('line_number')->nullable(); // Line number where error occurred
            $table->string('request_url')->nullable(); // URL where error occurred
            $table->string('user_agent')->nullable();
            $table->ipAddress('ip_address')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
            $table->index(['error_source', 'status']);
            $table->index(['user_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('error_logs');
    }
};
