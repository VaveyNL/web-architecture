<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GitHubController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('github')->redirect();
    }

    public function callback()
    {
        $githubUser = Socialite::driver('github')->user();

        $user = User::updateOrCreate(
            ['github_id' => $githubUser->getId()],
            [
                'name'     => $githubUser->getName() ?: $githubUser->getNickname(),
                'email'    => $githubUser->getEmail() ?: $githubUser->getNickname() . '@github.local',
                'password' => bcrypt(uniqid()),
            ]
        );

        Auth::login($user, remember: true);

        return redirect()->route('posts.index');
    }
}
