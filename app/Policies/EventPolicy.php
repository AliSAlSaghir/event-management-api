<?php

namespace App\Policies;

use App\Models\Event;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class EventPolicy {

  public function modify(User $user, Event $event): bool {
    return $user->id === $event->user_id;
  }
}
