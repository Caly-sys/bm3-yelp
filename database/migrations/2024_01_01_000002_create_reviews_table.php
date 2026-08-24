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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->tinyInteger('overall_rating');
            $table->tinyInteger('teaching_rating');
            $table->tinyInteger('explanation_rating');
            $table->tinyInteger('fairness_rating');
            $table->tinyInteger('workload_rating');
            $table->text('comment');
            $table->enum('status', ['published', 'hidden'])->default('published');
            $table->timestamps();

            // One review per student per teacher
            $table->unique(['teacher_id', 'user_id']);
            $table->index('teacher_id');
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
