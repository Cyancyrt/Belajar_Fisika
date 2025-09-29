<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\SimulationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



// Health check endpoint
Route::get('health', function () {
    return response()->json([
        'status' => 'success',
        'message' => 'Belajar Fisika API is running',
        'version' => '1.0.0',
        'timestamp' => now()
    ]);
});

// Public routes (tidak perlu authentication)
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
});

// Protected routes (perlu authentication)
Route::middleware('auth:sanctum')->group(function () {

    // Auth routes
    Route::prefix('auth')->group(function () {
        Route::get('profile', [AuthController::class, 'profile']);
        Route::post('logout', [AuthController::class, 'logout']);
    });

    // User routes
    Route::prefix('user')->group(function () {
        Route::get('/', function (Request $request) {
            return response()->json([
                'status' => 'success',
                'data' => [
                    'user' => $request->user()->load(['achievements.achievement', 'progress.topic'])
                ]
            ]);
        });

        Route::get('achievements', function (Request $request) {
            return response()->json([
                'status' => 'success',
                'data' => [
                    'achievements' => $request->user()->achievements()
                        ->with('achievement')
                        ->orderBy('earned_at', 'desc')
                        ->get()
                ]
            ]);
        });

        Route::get('progress', function (Request $request) {
            return response()->json([
                'status' => 'success',
                'data' => [
                    'progress' => $request->user()->progress()
                        ->with('topic')
                        ->orderBy('last_attempt_at', 'desc')
                        ->get()
                ]
            ]);
        });


        Route::get('stats', function (Request $request) {
            $user = $request->user();
            $attempts = $user->attempts();

            return response()->json([
                'status' => 'success',
                'data' => [
                    'stats' => [
                        'total_xp' => $user->total_xp,
                        'level' => $user->level,
                        'streak_days' => $user->streak_days,
                        'last_activity_date' => $user->last_activity_date, // ✅ Diperbaiki
                        'total_achievements' => $user->achievements()->count(),
                        'completed_topics' => $user->progress()->where('is_completed', true)->count(),
                        'total_attempts' => $attempts->count(),
                        'correct_attempts' => $attempts->where('is_correct', true)->count(),
                        'accuracy_rate' => $attempts->count() > 0
                            ? round(($attempts->where('is_correct', true)->count() / $attempts->count()) * 100, 2)
                            : 0,
                        'average_score' => round($attempts->avg('score_earned') ?? 0, 2)
                    ]
                ]
            ]);
        });
    });

    // Simulation routes - Main feature for mobile
    Route::prefix('simulation')->group(function () {
        Route::get('topics', [SimulationController::class, 'getTopics']);
        Route::get('topics/{topicSlug}/question', [SimulationController::class, 'getQuestion']);
        Route::post('questions/{questionId}/submit', [SimulationController::class, 'submitAnswer']);

        // User attempt history with pagination
        Route::get('attempts', function (Request $request) {
            $perPage = $request->get('per_page', 20);

            return response()->json([
                'status' => 'success',
                'data' => [
                    'attempts' => $request->user()->attempts()
                        ->with(['question.topic:id,name,slug'])
                        ->select(['id', 'simulation_question_id', 'is_correct', 'score_earned', 'created_at', 'time_taken'])
                        ->latest()
                        ->paginate($perPage)
                ]
            ]);
        });

        Route::get('attempts/{questionId}', function (Request $request, $questionId) {
            return response()->json([
                'status' => 'success',
                'data' => [
                    'attempts' => $request->user()->attempts()
                        ->where('simulation_question_id', $questionId)
                        ->with('question:id,question_text,max_score')
                        ->latest()
                        ->get()
                ]
            ]);
        });

        // Performance metrics for mobile dashboard
        Route::get('performance', function (Request $request) {
            $user = $request->user();
            $attempts = $user->attempts();

            // Weekly performance
            $weeklyAttempts = $attempts->where('created_at', '>=', now()->subWeek());

            return response()->json([
                'status' => 'success',
                'data' => [
                    'performance' => [
                        'overall' => [
                            'total_attempts' => $attempts->count(),
                            'correct_attempts' => $attempts->where('is_correct', true)->count(),
                            'accuracy_rate' => $attempts->count() > 0
                                ? round(($attempts->where('is_correct', true)->count() / $attempts->count()) * 100, 2)
                                : 0,
                            'average_score' => round($attempts->avg('score_earned') ?? 0, 2),
                        ],
                        'weekly' => [
                            'attempts' => $weeklyAttempts->count(),
                            'correct' => $weeklyAttempts->where('is_correct', true)->count(),
                            'accuracy' => $weeklyAttempts->count() > 0
                                ? round(($weeklyAttempts->where('is_correct', true)->count() / $weeklyAttempts->count()) * 100, 2)
                                : 0,
                        ],
                        'topics_mastered' => $user->progress()->where('is_completed', true)->count(),
                        'current_streak' => $user->streak_days,
                    ]
                ]
            ]);
        });
    });

    // Physics Topics routes
    Route::prefix('topics')->group(function () {
        Route::get('/', function (Request $request) {
            $user = $request->user();

            $topics = \App\Models\PhysicsTopic::with(['userProgress' => function ($query) use ($user) {
                $query->where('user_id', $user->id);
            }])
                ->where('is_active', true)
                ->orderBy('order_index')
                ->select(['id', 'name', 'slug', 'subtitle', 'difficulty', 'estimated_duration', 'icon', 'order_index'])
                ->get()
                ->map(function ($topic) {
                    $progress = $topic->userProgress->first();
                    $topic->progress = $progress ? [
                        'completed_questions' => $progress->completed_questions,
                        'total_questions' => $progress->total_questions,
                        'best_score' => $progress->best_score,
                        'is_completed' => $progress->is_completed,
                        'progress_percentage' => $progress->total_questions > 0
                            ? round(($progress->completed_questions / $progress->total_questions) * 100, 2)
                            : 0
                    ] : null;

                    unset($topic->userProgress);
                    return $topic;
                });

            return response()->json([
                'status' => 'success',
                'data' => ['topics' => $topics]
            ]);
        });

        Route::get('{slug}', function ($slug) {
            $topic = \App\Models\PhysicsTopic::where('slug', $slug)
                ->where('is_active', true)
                ->select(['id', 'name', 'slug', 'subtitle', 'description', 'difficulty', 'estimated_duration', 'icon'])
                ->firstOrFail();

            return response()->json([
                'status' => 'success',
                'data' => ['topic' => $topic]
            ]);
        });

        Route::get('{slug}/questions', function ($slug) {
            $topic = \App\Models\PhysicsTopic::where('slug', $slug)->firstOrFail();

            return response()->json([
                'status' => 'success',
                'data' => [
                    'questions' => $topic->questions()
                        ->where('is_active', true)
                        ->select(['id', 'question_text', 'simulation_type', 'max_score', 'difficulty'])
                        ->orderBy('difficulty')
                        ->get()
                ]
            ]);
        });
    });

    // Achievements routes
    Route::prefix('achievements')->group(function () {
        Route::get('/', function (Request $request) {
            $user = $request->user();
            $earnedIds = $user->achievements()->pluck('achievement_id')->toArray();

            $achievements = \App\Models\Achievement::where('is_active', true)
                ->select(['id', 'name', 'description', 'icon', 'xp_reward', 'criteria'])
                ->orderBy('xp_reward', 'desc')
                ->get()
                ->map(function ($achievement) use ($earnedIds) {
                    $achievement->is_earned = in_array($achievement->id, $earnedIds);
                    return $achievement;
                });

            return response()->json([
                'status' => 'success',
                'data' => ['achievements' => $achievements]
            ]);
        });

        Route::get('available', function (Request $request) {
            $user = $request->user();
            $earnedAchievementIds = $user->achievements()->pluck('achievement_id');

            return response()->json([
                'status' => 'success',
                'data' => [
                    'available_achievements' => \App\Models\Achievement::where('is_active', true)
                        ->whereNotIn('id', $earnedAchievementIds)
                        ->select(['id', 'name', 'description', 'icon', 'xp_reward', 'criteria'])
                        ->orderBy('xp_reward', 'asc')
                        ->get()
                ]
            ]);
        });
    });

    // Daily Challenge routes
    Route::prefix('challenges')->group(function () {
        Route::get('daily', function () {
            $challenge = \App\Models\DailyChallenge::whereDate('date', today())
                ->where('is_active', true)
                ->select(['id', 'title', 'description', 'difficulty', 'xp_reward', 'date'])
                ->first();

            return response()->json([
                'status' => 'success',
                'data' => ['challenge' => $challenge]
            ]);
        });

        Route::get('/', function () {
            return response()->json([
                'status' => 'success',
                'data' => [
                    'challenges' => \App\Models\DailyChallenge::where('is_active', true)
                        ->select(['id', 'title', 'description', 'difficulty', 'xp_reward', 'date'])
                        ->latest('date')
                        ->take(30)
                        ->get()
                ]
            ]);
        });
    });

    // Leaderboard endpoint for gamification
    Route::get('leaderboard', function (Request $request) {
        $currentUser = $request->user();
        $limit = $request->get('limit', 50);

        $topUsers = \App\Models\User::orderBy('total_xp', 'desc')
            ->take($limit)
            ->select(['id', 'name', 'total_xp', 'level'])
            ->get()
            ->map(function ($user, $index) {
                $user->rank = $index + 1;
                return $user;
            });

        // Find current user rank
        $currentUserRank = \App\Models\User::where('total_xp', '>', $currentUser->total_xp)->count() + 1;

        return response()->json([
            'status' => 'success',
            'data' => [
                'leaderboard' => $topUsers,
                'current_user_rank' => $currentUserRank,
                'current_user' => [
                    'id' => $currentUser->id,
                    'name' => $currentUser->name,
                    'total_xp' => $currentUser->total_xp,
                    'level' => $currentUser->level,
                    'rank' => $currentUserRank
                ]
            ]
        ]);
    });
});
// Fallback route untuk API
Route::fallback(function () {
    return response()->json([
        'status' => 'error',
        'message' => 'API endpoint not found'
    ], 404);
});


// Web routes dengan CSRF protection tetap aktif (jika diperlukan)
Route::get('/', function () {
    return view('Dashboard.index');
});
