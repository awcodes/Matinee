<?php

declare(strict_types=1);

namespace Workbench\Database\Seeders;

use Illuminate\Database\Seeder;
use Workbench\App\Models\Page;
use Workbench\App\Models\User;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        Page::factory()->create([
            'title' => 'Matinee Demo',
            'slug' => 'matinee-demo',
        ]);
    }
}
