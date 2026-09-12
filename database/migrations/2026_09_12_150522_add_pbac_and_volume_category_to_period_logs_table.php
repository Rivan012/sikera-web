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
        Schema::table('period_logs', function (Blueprint $table) {
            $table->string('volume_category')->nullable()->after('blood_consistency');
            $table->unsignedSmallInteger('pbac_score')->nullable()->after('volume_category');
            $table->json('pbac_details')->nullable()->after('pbac_score');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('period_logs', function (Blueprint $table) {
            $table->dropColumn(['volume_category', 'pbac_score', 'pbac_details']);
        });
    }
};
