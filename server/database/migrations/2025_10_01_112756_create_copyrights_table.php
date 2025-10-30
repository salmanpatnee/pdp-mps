<?php

use App\Models\Manuscript;
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
        Schema::create('copyrights', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Manuscript::class)->constrained()->cascadeOnDelete();
            $table->boolean('is_corporate_interest')->nullable()->default(false);
            $table->boolean('has_human_subjects')->nullable()->default(false);
            $table->boolean('has_animal_subjects')->nullable()->default(false);
            $table->boolean('is_conflict_interest')->nullable()->default(false);
            $table->boolean('has_us_government_author')->nullable()->default(false);
            $table->boolean('used_ai_technology')->nullable()->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('copyrights');
    }
};
