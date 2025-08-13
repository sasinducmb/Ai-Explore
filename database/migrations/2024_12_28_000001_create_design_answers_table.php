<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('design_answers', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('session_id');

            // Individual question results
            for ($i = 1; $i <= 10; $i++) {
                $table->text("question_{$i}_answer")->nullable();
                $table->boolean("question_{$i}_correct")->default(false);
                $table->integer("question_{$i}_marks")->default(0);
                $table->json("question_{$i}_analysis")->nullable();
                $table->timestamp("question_{$i}_completed_at")->nullable();
            }

            // Overall results
            $table->integer('total_marks')->default(0);
            $table->integer('total_possible_marks')->default(60);
            $table->decimal('percentage', 5, 2)->default(0);
            $table->string('grade')->nullable();
            $table->integer('completion_time_seconds')->nullable();
            $table->boolean('completed')->default(false);

            // Metadata
            $table->json('additional_data')->nullable();
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

    public function down(): void
    {
        Schema::dropIfExists('design_answers');
    }
};
