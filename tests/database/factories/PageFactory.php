<?php

declare(strict_types=1);

namespace Awcodes\Matinee\Tests\Database\Factories;

use Awcodes\Matinee\Tests\Fixtures\Models\Page;
use Illuminate\Database\Eloquent\Factories\Factory;

class PageFactory extends Factory
{
    protected $model = Page::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(),
            'slug' => $this->faker->slug(),
            'color' => null,
            'select_color' => null,
            'color_as_key' => null,
            'select_color_as_key' => null,
        ];
    }
}
