<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromptingAnswer extends Model
{
    use HasFactory;

    protected $table = 'prompting_answers';

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
        'question_3_answer',
        'question_3_correct',
        'question_3_marks',
        'question_3_analysis',
        'question_3_completed_at',
        'question_4_answer',
        'question_4_correct',
        'question_4_marks',
        'question_4_analysis',
        'question_4_completed_at',
        'question_5_answer',
        'question_5_correct',
        'question_5_marks',
        'question_5_analysis',
        'question_5_completed_at',
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
        'question_4_correct' => 'boolean',
        'question_5_correct' => 'boolean',
        'question_2_analysis' => 'array',
        'question_3_analysis' => 'array',
        'question_4_analysis' => 'array',
        'question_5_analysis' => 'array',
        'question_1_completed_at' => 'datetime',
        'question_2_completed_at' => 'datetime',
        'question_3_completed_at' => 'datetime',
        'question_4_completed_at' => 'datetime',
        'question_5_completed_at' => 'datetime',
        'percentage' => 'decimal:2',
        'completed' => 'boolean',
        'additional_data' => 'array',
    ];

    /**
     * Calculate progress percentage based on completed questions
     */
    public function getProgressPercentage()
    {
        $completedQuestions = 0;
        $totalQuestions = 5;

        for ($i = 1; $i <= 5; $i++) {
            if (!empty($this->{"question_{$i}_answer"})) {
                $completedQuestions++;
            }
        }

        return ($completedQuestions / $totalQuestions) * 100;
    }

    /**
     * Get the number of completed questions
     */
    public function getCompletedQuestionsCount()
    {
        $count = 0;
        for ($i = 1; $i <= 5; $i++) {
            if (!empty($this->{"question_{$i}_answer"})) {
                $count++;
            }
        }
        return $count;
    }

    /**
     * Check if a specific question is completed
     */
    public function isQuestionCompleted($questionNumber)
    {
        return !empty($this->{"question_{$questionNumber}_answer"});
    }

    /**
     * Get the current question number (next incomplete question)
     */
    public function getCurrentQuestionNumber()
    {
        for ($i = 1; $i <= 5; $i++) {
            if (empty($this->{"question_{$i}_answer"})) {
                return $i;
            }
        }
        return 5; // All questions completed
    }

    /**
     * Update totals and calculate grade
     */
    public function updateTotals()
    {
        $totalMarks = 0;
        for ($i = 1; $i <= 5; $i++) {
            $totalMarks += $this->{"question_{$i}_marks"} ?? 0;
        }

        $percentage = ($totalMarks / $this->total_possible_marks) * 100;
        $grade = $this->calculateGrade($percentage);

        $this->update([
            'total_marks' => $totalMarks,
            'percentage' => $percentage,
            'grade' => $grade,
        ]);

        return $this;
    }

    /**
     * Calculate grade based on percentage
     */
    private function calculateGrade($percentage)
    {
        if ($percentage >= 90) return 'A';
        if ($percentage >= 80) return 'B';
        if ($percentage >= 70) return 'C';
        if ($percentage >= 60) return 'D';
        return 'F';
    }

    /**
     * Get statistics for all results
     */
    public static function getStatistics()
    {
        return [
            'total_attempts' => self::count(),
            'completed_attempts' => self::where('completed', true)->count(),
            'average_score' => self::where('completed', true)->avg('percentage') ?? 0,
            'highest_score' => self::where('completed', true)->max('percentage') ?? 0,
            'lowest_score' => self::where('completed', true)->min('percentage') ?? 0,
        ];
    }

    /**
     * Scope for session-based queries
     */
    public function scopeBySession($query, $sessionId)
    {
        return $query->where('session_id', $sessionId);
    }

    /**
     * Scope for completed quizzes
     */
    public function scopeCompleted($query)
    {
        return $query->where('completed', true);
    }

    /**
     * Get formatted completion time
     */
    public function getFormattedCompletionTime()
    {
        if (!$this->completion_time_seconds) {
            return 'N/A';
        }

        $minutes = floor($this->completion_time_seconds / 60);
        $seconds = $this->completion_time_seconds % 60;

        if ($minutes > 0) {
            return sprintf('%d min %d sec', $minutes, $seconds);
        }

        return sprintf('%d sec', $seconds);
    }

    /**
     * Get all question answers as an array
     */
    public function getAllAnswers()
    {
        $answers = [];
        for ($i = 1; $i <= 5; $i++) {
            $answers[$i] = [
                'answer' => $this->{"question_{$i}_answer"},
                'correct' => $this->{"question_{$i}_correct"},
                'marks' => $this->{"question_{$i}_marks"},
                'completed_at' => $this->{"question_{$i}_completed_at"},
                'analysis' => $this->{"question_{$i}_analysis"},
            ];
        }
        return $answers;
    }
}
