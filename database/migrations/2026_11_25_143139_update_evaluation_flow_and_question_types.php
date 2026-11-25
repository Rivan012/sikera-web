<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('test_questions', function (Blueprint $table): void {
            $table->string('question_type', 20)->default('pre_test')->after('id');
            $table->integer('module_target')->nullable()->change();
            $table->index(['question_type', 'module_target']);
        });

        Schema::table('users', function (Blueprint $table): void {
            $table->timestamp('all_modules_completed_at')->nullable()->after('posttest_completed');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn('all_modules_completed_at');
        });

        Schema::table('test_questions', function (Blueprint $table): void {
            $table->dropIndex(['question_type', 'module_target']);
            $table->dropColumn('question_type');
        });
    }
};
