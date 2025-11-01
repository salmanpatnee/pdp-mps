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
        Schema::create('manuscript_co_authors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('manuscript_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('name');
            $table->string('email');
            $table->string('affiliation')->nullable();
            $table->string('country')->nullable();
            $table->boolean('is_principal')->default(false);
            $table->unsignedInteger('order')->default(1); // For manual sorting
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manuscript_co_authors');
    }
};
