<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PromptingResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'session_id',
        'question_1_answer',
        'question_1_correct',
        'question_1_marks',
        'question_1_completed_at',
        'question_2_answer',
        'question_2_correct',
        'question_2_marks',
        'question_2_analysis',
        'question_2_completed_at',
        'question_3_topic',
        'question_3_answer',
        'question_3_correct',
        'question_3_marks',
        'question_3_analysis',
        'question_3_completed_at',
        'total_marks',
        'total_possible_marks',
        'percentage',
        'grade',
        'completion_time_seconds',
        'completed',
        'additional_data',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'question_1_correct' => 'boolean',
        'question_2_correct' => 'boolean',
        'question_3_correct' => 'boolean',
        'question_2_analysis' => 'array',
        'question_3_analysis' => 'array',
        'additional_data' => 'array',
        'completed' => 'boolean',
        'percentage' => 'decimal:2',
        'question_1_completed_at' => 'datetime',
        'question_2_completed_at' => 'datetime',
        'question_3_completed_at' => 'datetime',
    ];

    /**
     * Calculate grade based on percentage
     */
    public function calculateGrade(): string
    {
        $percentage = $this->percentage;

        if ($percentage >= 90) return 'A+';
        if ($percentage >= 85) return 'A';
        if ($percentage >= 80) return 'A-';
        if ($percentage >= 75) return 'B+';
        if ($percentage >= 70) return 'B';
        if ($percentage >= 65) return 'B-';
        if ($percentage >= 60) return 'C+';
        if ($percentage >= 55) return 'C';
        if ($percentage >= 50) return 'C-';
        if ($percentage >= 45) return 'D+';
        if ($percentage >= 40) return 'D';
        return 'F';
    }

    /**
     * Update total marks and percentage
     */
    public function updateTotals(): void
    {
        $this->total_marks = $this->question_1_marks + $this->question_2_marks + $this->question_3_marks;
        $this->percentage = ($this->total_marks / $this->total_possible_marks) * 100;
        $this->grade = $this->calculateGrade();
        $this->save();
    }

    /**
     * Get completion time in human readable format
     */
    public function getFormattedCompletionTimeAttribute(): string
    {
        if (!$this->completion_time_seconds) {
            return 'Not completed';
        }

        $minutes = floor($this->completion_time_seconds / 60);
        $seconds = $this->completion_time_seconds % 60;

        if ($minutes > 0) {
            return "{$minutes}m {$seconds}s";
        }

        return "{$seconds}s";
    }

    /**
     * Check if all questions are completed
     */
    public function isFullyCompleted(): bool
    {
        return $this->question_1_completed_at &&
            $this->question_2_completed_at &&
            $this->question_3_completed_at;
    }

    /**
     * Get progress percentage
     */
    public function getProgressPercentage(): float
    {
        $completedQuestions = 0;
        if ($this->question_1_completed_at) $completedQuestions++;
        if ($this->question_2_completed_at) $completedQuestions++;
        if ($this->question_3_completed_at) $completedQuestions++;

        return ($completedQuestions / 3) * 100;
    }

    /**
     * Scope for completed results
     */
    public function scopeCompleted($query)
    {
        return $query->where('completed', true);
    }

    /**
     * Scope for results by session
     */
    public function scopeBySession($query, $sessionId)
    {
        return $query->where('session_id', $sessionId);
    }

    /**
     * Get statistics for analytics
     */
    public static function getStatistics(): array
    {
        $completed = self::completed();

        return [
            'total_attempts' => self::count(),
            'completed_attempts' => $completed->count(),
            'average_score' => $completed->avg('percentage') ?? 0,
            'highest_score' => $completed->max('percentage') ?? 0,
            'lowest_score' => $completed->min('percentage') ?? 0,
            'average_completion_time' => $completed->avg('completion_time_seconds') ?? 0,
            'grade_distribution' => $completed->groupBy('grade')
                ->map(function ($group) {
                    return $group->count();
                })->toArray(),
        ];
    }
}
