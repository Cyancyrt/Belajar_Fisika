<?php

// app/Events/UserLeveledUp.php
namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserLeveledUp
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $newLevel;
    public $oldLevel;

    public function __construct(User $user, $newLevel)
    {
        $this->user = $user;
        $this->newLevel = $newLevel;
        $this->oldLevel = $newLevel - 1;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('user.' . $this->user->id);
    }
}