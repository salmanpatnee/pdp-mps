<?php

use App\Models\Manuscript;
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
        Schema::create('manuscript_files', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Manuscript::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(User::class, 'uploaded_by');
            $table->string('file_name');
            $table->string('file_path');
            $table->enum('file_type', ['manuscript', 'figure', 'supplementary', 'copyright', 'rebuttal', 'table', 'graphical _abstract', 'scheme', 'copyright_letter', 'other', 'structured_abstract', 'language_editing_certificate', 'proof_reading_file', 'epub_fulltext']);
            $table->string('file_extension')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manuscript_files');
    }
};
