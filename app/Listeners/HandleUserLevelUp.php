<?php

namespace App\Listeners;

use App\Events\UserLeveledUp;
use App\Models\Achievement;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class HandleUserLevelUp
{
    public function handle(UserLeveledUp $event)
    {
        $user = $event->user;
        $newLevel = $event->newLevel;

        // Log level up
        Log::info("User {$user->name} leveled up to {$newLevel}");

        // Check for level-based achievements
        $this->checkLevelAchievements($user, $newLevel);

        // Optional: Send notification, email, dll
        // $user->notify(new LevelUpNotification($newLevel));
    }

    private function checkLevelAchievements($user, $level)
    {
        // Check achievements yang unlock di level tertentu
        $levelAchievements = Achievement::where('is_active', true)
            ->whereJsonContains('criteria->level', $level)
            ->whereNotIn('id', $user->achievements()->pluck('achievement_id'))
            ->get();

        foreach ($levelAchievements as $achievement) {
            $user->achievements()->attach($achievement->id, ['earned_at' => now()]);
            $user->increment('total_xp', $achievement->xp_reward);
            
            Log::info("User {$user->name} earned achievement: {$achievement->name}");
        }
    }
}
