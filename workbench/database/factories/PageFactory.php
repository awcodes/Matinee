<?php

declare(strict_types=1);

namespace Workbench\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Workbench\App\Models\Page;

class PageFactory extends Factory
{
    protected $model = Page::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(),
            'slug' => $this->faker->slug(),
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
        ];
    }
}
