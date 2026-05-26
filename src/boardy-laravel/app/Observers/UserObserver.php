<?php

namespace App\Observers;

use App\Models\User;
use Illuminate\Support\Facades\Redis;

class UserObserver
{
    public function updated(User $user): void
    {
        if ($user->wasChanged('name')) {
            Redis::publish('user.renamed', json_encode([
                'id'       => $user->id,
                'new_name' => $user->name,
            ]));
        }
    }
}
