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
        Schema::table('copyrights', function (Blueprint $table) {
            $table->string('human_subjects_approval_path')->nullable()->after('has_human_subjects');
            $table->string('animal_subjects_approval_path')->nullable()->after('has_animal_subjects');
            $table->text('conflict_of_interest_details')->nullable()->after('is_conflict_interest');
            $table->string('us_government_permission_path')->nullable()->after('has_us_government_author');
            $table->text('ai_usage_details')->nullable()->after('used_ai_technology');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('copyrights', function (Blueprint $table) {
            $table->dropColumn([
                'human_subjects_approval_path',
                'animal_subjects_approval_path',
                'conflict_of_interest_details',
                'us_government_permission_path',
                'ai_usage_details',
            ]);
        });
    }
};
