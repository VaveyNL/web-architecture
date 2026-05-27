<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Тестовый пользователь - для входа
        $testUser = User::factory()->create([
            'name'     => 'Тест',
            'email'    => 'test@boardy.local',
            'password' => bcrypt('password'),
        ]);

        // 4 случайных пользователя
        $users = User::factory()->count(4)->create();

        // Все пользователи (тестовый + случайные)
        $allUsers = $users->push($testUser);

        // 10 постов от случайных пользователей
        $posts = Post::factory()->count(10)->create([
            'user_id' => fn() => $allUsers->random()->id,
        ]);

        // 25 комментариев на случайные посты от случайных пользователей
        Comment::factory()->count(25)->create([
            'post_id' => fn() => $posts->random()->id,
            'user_id' => fn() => $allUsers->random()->id,
        ]);
    }
}
