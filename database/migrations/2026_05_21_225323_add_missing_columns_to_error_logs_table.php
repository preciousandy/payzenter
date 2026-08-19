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
        Schema::table('error_logs', function (Blueprint $table) {
            if (!Schema::hasColumn('error_logs', 'user_id')) {
                $table->foreignId('user_id')->nullable()->after('id')->constrained()->nullOnDelete();
            }
            if (!Schema::hasColumn('error_logs', 'error_source')) {
                $table->string('error_source')->nullable()->after('user_id');
            }
            if (!Schema::hasColumn('error_logs', 'error_message')) {
                $table->text('error_message')->nullable()->after('error_source');
            }
            if (!Schema::hasColumn('error_logs', 'error_details')) {
                $table->text('error_details')->nullable()->after('error_message');
            }
            if (!Schema::hasColumn('error_logs', 'error_code')) {
                $table->string('error_code')->nullable()->after('error_details');
            }
            if (!Schema::hasColumn('error_logs', 'severity')) {
                $table->string('severity')->default('medium')->after('status');
            }
            if (!Schema::hasColumn('error_logs', 'file_path')) {
                $table->string('file_path')->nullable()->after('severity');
            }
            if (!Schema::hasColumn('error_logs', 'line_number')) {
                $table->integer('line_number')->nullable()->after('file_path');
            }
            if (!Schema::hasColumn('error_logs', 'request_url')) {
                $table->string('request_url')->nullable()->after('line_number');
            }
            if (!Schema::hasColumn('error_logs', 'user_agent')) {
                $table->string('user_agent')->nullable()->after('request_url');
            }
            if (!Schema::hasColumn('error_logs', 'ip_address')) {
                $table->string('ip_address')->nullable()->after('user_agent');
            }
            if (!Schema::hasColumn('error_logs', 'resolved_at')) {
                $table->timestamp('resolved_at')->nullable()->after('ip_address');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('error_logs', function (Blueprint $table) {
            //
        });
    }
};
