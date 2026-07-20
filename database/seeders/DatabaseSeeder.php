<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * 填充应用数据库。
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => '测试用户',
            'email' => 'test@example.com',
        ]);
    }
}
