<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\PhysicsTopic;
use App\Models\SimulationAttempt;
use App\Models\SimulationQuestion;
use App\Models\UserProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SimulationController extends Controller
{
    public function getTopics()
    {
        $user = Auth::user();
        
        $topics = PhysicsTopic::with(['userProgress' => function($query) use ($user) {
            $query->where('user_id', $user->id);
        }])
        ->where('is_active', true)
        ->orderBy('order_index')
        ->get()
        ->map(function($topic) {
            $progress = $topic->userProgress->first();
            
            return [
                'id' => $topic->id,
                'name' => $topic->name,
                'slug' => $topic->slug,
                'subtitle' => $topic->subtitle,
                'difficulty' => $topic->difficulty,
                'estimated_duration' => $topic->estimated_duration,
                'icon' => $topic->icon,
                'progress' => $progress ? [
                    'completed_questions' => $progress->completed_questions,
                    'total_questions' => $progress->total_questions,
                    'best_score' => $progress->best_score,
                    'is_completed' => $progress->is_completed,
                ] : null,
            ];
        });

        return response()->json(['topics' => $topics]);
    }

    public function getQuestion($topicSlug)
    {
        $topic = PhysicsTopic::where('slug', $topicSlug)->firstOrFail();
        
        // Get random question atau next question based on progress
        $question = SimulationQuestion::where('physics_topic_id', $topic->id)
                                    ->where('is_active', true)
                                    ->inRandomOrder()
                                    ->first();

        return response()->json([
            'question' => [
                'id' => $question->id,
                'question_text' => $question->question_text,
                'simulation_type' => $question->simulation_type,
                'parameters' => $question->parameters,
                'evaluation_criteria' => $question->evaluation_criteria,
                'hints' => $question->hints,
                'max_score' => $question->max_score,
            ]
        ]);
    }

    public function submitAnswer(Request $request, $questionId)
    {
        $request->validate([
            'user_answer' => 'required|array',
            'time_taken' => 'nullable|numeric',
            'simulation_data' => 'nullable|array',
        ]);

        $question = SimulationQuestion::findOrFail($questionId);
        $user = Auth::user();

        // Calculate score based on evaluation criteria
        $isCorrect = $this->evaluateAnswer($request->user_answer, $question->evaluation_criteria);
        $scoreEarned = $isCorrect ? $question->max_score : 0;

        // Save attempt
        $attempt = SimulationAttempt::create([
            'user_id' => $user->id,
            'simulation_question_id' => $questionId,
            'user_answer' => $request->user_answer,
            'correct_answer' => $question->evaluation_criteria,
            'is_correct' => $isCorrect,
            'score_earned' => $scoreEarned,
            'attempt_number' => $user->attempts()->where('simulation_question_id', $questionId)->count() + 1,
            'time_taken' => $request->time_taken,
            'simulation_data' => $request->simulation_data,
        ]);

        // Update user XP and progress
        if ($isCorrect) {
            $user->increment('total_xp', $scoreEarned);
            $this->updateUserProgress($user, $question);
        }

        return response()->json([
            'is_correct' => $isCorrect,
            'score_earned' => $scoreEarned,
            'total_xp' => $user->total_xp,
            'feedback' => $this->generateFeedback($isCorrect, $question),
        ]);
    }

    private function evaluateAnswer($userAnswer, $criteria)
    {
        // Logic untuk evaluate jawaban based on criteria
        $targetValue = $criteria['target_value'];
        $tolerance = $criteria['tolerance'];
        $userValue = $userAnswer['calculated_value'] ?? 0;

        return abs($userValue - $targetValue) <= $tolerance;
    }

    private function updateUserProgress($user, $question)
    {
        $progress = UserProgress::firstOrCreate(
            [
                'user_id' => $user->id,
                'physics_topic_id' => $question->physics_topic_id,
            ],
            [
                'total_questions' => SimulationQuestion::where('physics_topic_id', $question->physics_topic_id)->count(),
            ]
        );

        $progress->increment('completed_questions');
        $progress->best_score = max($progress->best_score, $question->max_score);
        $progress->last_attempt_at = now();
        
        if ($progress->completed_questions >= $progress->total_questions) {
            $progress->is_completed = true;
        }
        
        $progress->save();
    }

    private function generateFeedback($isCorrect, $question)
    {
        if ($isCorrect) {
            return [
                'message' => 'Congratulations! Your answer is correct.',
                'explanation' => 'You have successfully solved this simulation problem.',
                'hints' => null,
            ];
        } else {
            return [
                'message' => 'Your answer is incorrect. Try again!',
                'explanation' => 'Please review the problem and check your calculations.',
                'hints' => $question->hints,
            ];
        }
    }
}
