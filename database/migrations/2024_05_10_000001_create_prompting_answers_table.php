<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prompting_answers', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable(); // If you have user authentication
            $table->string('session_id'); // To track anonymous users

            // Question 1 - MCQ
            $table->string('question_1_answer')->nullable();
            $table->boolean('question_1_correct')->default(false);
            $table->integer('question_1_marks')->default(0);
            $table->timestamp('question_1_completed_at')->nullable();

            // Question 2 - Reverse Prompt Builder
            $table->text('question_2_answer')->nullable();
            $table->boolean('question_2_correct')->default(false);
            $table->integer('question_2_marks')->default(0);
            $table->json('question_2_analysis')->nullable();
            $table->timestamp('question_2_completed_at')->nullable();

            // Question 3 - Super Question Builder
            $table->text('question_3_answer')->nullable();
            $table->boolean('question_3_correct')->default(false);
            $table->integer('question_3_marks')->default(0);
            $table->json('question_3_analysis')->nullable();
            $table->timestamp('question_3_completed_at')->nullable();

            // Question 4
            $table->text('question_4_answer')->nullable();
            $table->boolean('question_4_correct')->default(false);
            $table->integer('question_4_marks')->default(0);
            $table->json('question_4_analysis')->nullable();
            $table->timestamp('question_4_completed_at')->nullable();

            // Question 5
            $table->text('question_5_answer')->nullable();
            $table->boolean('question_5_correct')->default(false);
            $table->integer('question_5_marks')->default(0);
            $table->json('question_5_analysis')->nullable();
            $table->timestamp('question_5_completed_at')->nullable();

            // Overall results
            $table->integer('total_marks')->default(0);
            $table->integer('total_possible_marks')->default(30);
            $table->decimal('percentage', 5, 2)->default(0);
            $table->string('grade')->nullable(); // A, B, C, D, F
            $table->integer('completion_time_seconds')->nullable(); // Time taken to complete
            $table->boolean('completed')->default(false);

            // Metadata
            $table->json('additional_data')->nullable(); // For any extra analysis data
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();

            $table->timestamps();

            // Indexes
            $table->index('session_id');
            $table->index('completed');
            $table->index('percentage');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prompting_answers');
    }
};
