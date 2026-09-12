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
            $table->unsignedSmallInteger('menarche_age')->nullable()->after('user_id');
            $table->boolean('is_regular')->default(true)->after('cycle_length');
            $table->string('blood_consistency')->nullable()->after('flow_color');
            $table->boolean('has_dysmenorrhea')->default(false)->after('nrs_pain_score');
            $table->unsignedTinyInteger('walidd_working_ability')->nullable()->after('has_dysmenorrhea');
            $table->json('walidd_locations')->nullable()->after('walidd_working_ability');
            $table->unsignedTinyInteger('walidd_location_score')->nullable()->after('walidd_locations');
            $table->unsignedTinyInteger('walidd_intensity_score')->nullable()->after('walidd_location_score');
            $table->unsignedSmallInteger('walidd_pain_days')->nullable()->after('walidd_intensity_score');
            $table->unsignedTinyInteger('walidd_pain_days_score')->nullable()->after('walidd_pain_days');
            $table->unsignedTinyInteger('walidd_total_score')->nullable()->after('walidd_pain_days_score');
            $table->string('walidd_category')->nullable()->after('walidd_total_score');
            $table->text('walidd_interpretation')->nullable()->after('walidd_category');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->unsignedSmallInteger('menarche_age')->nullable()->after('usia');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('period_logs', function (Blueprint $table) {
            $table->dropColumn([
                'menarche_age',
                'is_regular',
                'blood_consistency',
                'has_dysmenorrhea',
                'walidd_working_ability',
                'walidd_locations',
                'walidd_location_score',
                'walidd_intensity_score',
                'walidd_pain_days',
                'walidd_pain_days_score',
                'walidd_total_score',
                'walidd_category',
                'walidd_interpretation',
            ]);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('menarche_age');
        });
    }
};
