<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('google_sheet_sync_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('event_type'); // 'pre_test_submission', 'post_test_submission', 'biodata_registered'
            $table->string('student_identifier'); // NIM / Nama Inisial
            $table->string('fakultas_prodi')->nullable();
            $table->json('payload_data'); // Data demografi + skor
            $table->enum('sync_status', ['success', 'pending', 'failed'])->default('success');
            $table->string('sheet_range')->nullable(); // e.g. 'Sheet1!A12:K12'
            $table->timestamp('synced_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('google_sheet_sync_logs');
    }
};
