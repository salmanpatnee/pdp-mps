<?php

use App\Models\ArticleType;
use App\Models\Journal;
use App\Models\User;
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
        Schema::create('manuscripts', function (Blueprint $table) {
            $table->id();
            $table->string('reference_no')->unique();
            $table->foreignIdFor(Journal::class)->constrained();
            $table->foreignIdFor(User::class)->constrained();
            $table->foreignIdFor(ArticleType::class)->constrained();
            $table->enum('submission_type', ['manuscript', 'proposed_abstract', 'thematic_issue']);
            $table->string('title');
            $table->text('abstract')->nullable();
            $table->text('keywords')->nullable();
            $table->enum('status', ['Awaiting Editorial Approval', 'submitted', 'under_review', 'revision', 'accepted', 'rejected'])->default('Awaiting Editorial Approval');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manuscripts');
    }
};
