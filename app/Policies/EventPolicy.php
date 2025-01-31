<?php

namespace App\Policies;

use App\Models\Events;
use App\Models\User;
use Illuminate\Auth\Access\Response;


class EventPolicy
{

    // public function modify(User $user, Events $event): Response
    // {
    //     return $user->id === $event->user_id
    //         ? Response::allow()
    //         : Response::deny('You do not own this event.');
    // }
}
