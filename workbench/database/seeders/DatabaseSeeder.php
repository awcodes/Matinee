<?php

declare(strict_types=1);

namespace Workbench\Database\Seeders;

use Carbon\CarbonImmutable;
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

        // Fixed pages at fixed dates, with the payloads the field itself would store, so documentation
        // screenshots are the same on every build. They cover both built-in providers, a responsive and a
        // fixed-size embed, and YouTube's nocookie and start options.
        $pages = [
            [
                'title' => 'Matinee Demo',
                'slug' => 'matinee-demo',
                'video' => [
                    'width' => '16',
                    'height' => '9',
                    'responsive' => true,
                    'url' => 'https://www.youtube.com/watch?v=N9qZFD1NkhI',
                    'embed_url' => 'https://www.youtube.com/embed/N9qZFD1NkhI?controls=1&start=0',
                    'options' => [
                        'controls' => '1',
                        'nocookie' => '0',
                        'start' => '00:00:00',
                    ],
                ],
            ],
            [
                'title' => 'Fixed-size Vimeo embed',
                'slug' => 'fixed-size-vimeo-embed',
                'video' => [
                    'width' => '640',
                    'height' => '480',
                    'responsive' => false,
                    'url' => 'https://vimeo.com/76979871',
                    'embed_url' => 'https://player.vimeo.com/video/76979871?autoplay=0&loop=0&show_title=1&byline=0&portrait=0',
                    'options' => [
                        'autoplay' => '0',
                        'loop' => '0',
                        'show_title' => '1',
                        'byline' => '0',
                        'portrait' => '0',
                    ],
                ],
            ],
            [
                'title' => 'Privacy-enhanced YouTube embed',
                'slug' => 'privacy-enhanced-youtube-embed',
                'video' => [
                    'width' => '16',
                    'height' => '9',
                    'responsive' => true,
                    'url' => 'https://youtu.be/aqz-KE-bpKQ',
                    'embed_url' => 'https://www.youtube-nocookie.com/embed/aqz-KE-bpKQ?controls=0&start=90',
                    'options' => [
                        'controls' => '0',
                        'nocookie' => '1',
                        'start' => '00:01:30',
                    ],
                ],
            ],
        ];

        foreach ($pages as $index => $page) {
            $date = CarbonImmutable::parse('2026-01-01 09:00:00')->addDays($index);

            Page::query()->create([
                ...$page,
                'created_at' => $date,
                'updated_at' => $date,
            ]);
        }
    }
}
