<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Modul Pembelajaran
        Schema::create('educational_modules', function (Blueprint $table) {
            $table->id();
            $table->integer('module_number')->unique();
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->text('description');
            $table->string('badge_icon')->nullable();
            $table->string('banner_image')->nullable();
            $table->string('estimated_time')->default('10 menit');
            $table->timestamps();
        });

        // 2. Submateri per Modul
        Schema::create('module_topics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('educational_module_id')->constrained()->cascadeOnDelete();
            $table->string('topic_code'); // e.g., 1.1, 1.2
            $table->string('title');
            $table->longText('content_html');
            $table->string('youtube_video_id')->nullable();
            $table->string('infographic_url')->nullable();
            $table->integer('order_index')->default(1);
            $table->timestamps();
        });

        // 3. User Progress Modul
        Schema::create('module_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('module_topic_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_completed')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'module_topic_id']);
        });

        // 4. Soal Pre-Test & Post-Test
        Schema::create('test_questions', function (Blueprint $table) {
            $table->id();
            $table->integer('module_target')->default(1); // Modul 1-4
            $table->text('question_text');
            $table->json('options'); // [{label: 'A', text: '...'}, ...]
            $table->string('correct_answer'); // 'A' / 'B' / 'C' / 'D'
            $table->text('explanation')->nullable();
            $table->timestamps();
        });

        // 5. Riwayat Jawaban Evaluasi (Pre & Post)
        Schema::create('evaluation_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['pre_test', 'post_test']);
            $table->integer('module_number')->nullable(); // 1, 2, 3, 4, atau null (keseluruhan)
            $table->json('raw_answers'); // {question_id: 'A', ...}
            $table->json('scores_per_module')->nullable(); // {modul_1: 80, modul_2: 100, ...}
            $table->integer('total_score'); // 0 - 100
            $table->float('n_gain_score')->nullable(); // kalkulasi n-gain khusus post-test
            $table->timestamp('submitted_at');
            $table->timestamps();
        });

        // 6. Period & Menstrual Tracker Logs
        Schema::create('period_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->integer('cycle_length')->default(28);
            $table->integer('period_duration')->default(5);
            $table->enum('flow_level', ['ringan', 'sedang', 'deras', 'sangat_deras'])->default('sedang');
            $table->string('flow_color')->nullable(); // Merah Terang, Cokelat, Merah Muda, Kehitaman
            $table->integer('nrs_pain_score')->default(0); // Skala 0-10
            $table->json('symptoms')->nullable(); // kram, kembung, migrain, lelah
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 7. BMI Calculator History
        Schema::create('bmi_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->float('weight_kg');
            $table->float('height_cm');
            $table->float('bmi_value');
            $table->string('category'); // Kurus (KEK), Normal, Overweight, Obesitas
            $table->text('advice')->nullable();
            $table->timestamps();
        });

        // 8. KesproFeed (Poster Galeri Edukatif)
        Schema::create('kespro_feeds', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('category'); // Higienitas, Relasi, Medis, Mitos-Fakta
            $table->string('image_path');
            $table->text('caption');
            $table->string('share_text')->nullable();
            $table->integer('download_count')->default(0);
            $table->integer('share_count')->default(0);
            $table->timestamps();
        });

        // 9. Case Studies (Studi Kasus Lingkungan Mahasiswa)
        Schema::create('case_studies', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('category'); // Living Together, Gaslighting, IMS, KTD
            $table->text('narrative'); // Cerita kasus riil
            $table->text('legal_analysis'); // Aspek hukum & UU TPKS
            $table->text('medical_analysis'); // Aspek kesehatan medis
            $table->text('solution_tips'); // Solusi & langkah asertif
            $table->timestamps();
        });

        // 10. Anonymous Forum & QA
        Schema::create('forum_threads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('anonymous_alias'); // Misal: "Mahasiswa Mawar #123"
            $table->string('title');
            $table->string('topic'); // Haid, Hubungan, Medis, Darurat
            $table->text('content');
            $table->boolean('is_answered_by_counselor')->default(false);
            $table->timestamps();
        });

        Schema::create('forum_replies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('forum_thread_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('display_name'); // "Dosen PA Konselor" atau alias anonim
            $table->enum('role_badge', ['dosen_pa', 'admin', 'mahasiswa'])->default('mahasiswa');
            $table->text('reply_content');
            $table->timestamps();
        });

        // 11. Secret Diary & Mood Log
        Schema::create('secret_diaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('mood'); // Senang, Cemas, Lelah, Sedih, Tenang
            $table->text('encrypted_note');
            $table->date('entry_date');
            $table->timestamps();
        });

        // 12. Daily Trivia & Myth-Fact Gamification
        Schema::create('trivia_questions', function (Blueprint $table) {
            $table->id();
            $table->text('question');
            $table->json('choices'); // array of options
            $table->string('answer_key');
            $table->text('scientific_explanation');
            $table->timestamps();
        });

        Schema::create('myth_fact_cards', function (Blueprint $table) {
            $table->id();
            $table->text('statement');
            $table->boolean('is_fact'); // true = fakta, false = mitos
            $table->text('scientific_fact');
            $table->string('category');
            $table->timestamps();
        });

        Schema::create('user_daily_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('activity_date');
            $table->string('activity_type'); // 'trivia', 'myth_fact', 'tracker_log'
            $table->integer('points_earned')->default(10);
            $table->timestamps();
            $table->unique(['user_id', 'activity_date', 'activity_type']);
        });

        // 13. Hotlines & Emergency Contacts
        Schema::create('emergency_hotlines', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('category'); // PPKS, Konseling, RS/Faskes
            $table->string('phone_number');
            $table->string('whatsapp_number')->nullable();
            $table->string('address')->nullable();
            $table->string('operating_hours')->default('24 Jam');
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('emergency_hotlines');
        Schema::dropIfExists('user_daily_activities');
        Schema::dropIfExists('myth_fact_cards');
        Schema::dropIfExists('trivia_questions');
        Schema::dropIfExists('secret_diaries');
        Schema::dropIfExists('forum_replies');
        Schema::dropIfExists('forum_threads');
        Schema::dropIfExists('case_studies');
        Schema::dropIfExists('kespro_feeds');
        Schema::dropIfExists('bmi_logs');
        Schema::dropIfExists('period_logs');
        Schema::dropIfExists('evaluation_responses');
        Schema::dropIfExists('test_questions');
        Schema::dropIfExists('module_progress');
        Schema::dropIfExists('module_topics');
        Schema::dropIfExists('educational_modules');
    }
};
