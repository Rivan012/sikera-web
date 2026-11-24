<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('module_topics', function (Blueprint $table): void {
            $table->longText('content_html')->nullable()->change();
            $table->string('pdf_path')->nullable()->after('infographic_url');
            $table->string('pdf_original_name')->nullable()->after('pdf_path');
            $table->unsignedBigInteger('pdf_size')->nullable()->after('pdf_original_name');
        });
    }

    public function down(): void
    {
        Schema::table('module_topics', function (Blueprint $table): void {
            $table->dropColumn(['pdf_path', 'pdf_original_name', 'pdf_size']);
        });
    }
};
