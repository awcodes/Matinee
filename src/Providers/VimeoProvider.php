<?php

namespace Awcodes\Matinee\Providers;

use Closure;

class VimeoProvider implements Contracts\MatineeProvider
{
    use Concerns\IsProvider;

    protected string | Closure | null $id = 'vimeo';

    public function shouldShow(string $url): bool
    {
        return str_contains($url, 'vimeo');
    }

    public function getOptions(): array
    {
        return [
            'autoplay' => __('matinee::oembed.autoplay'),
            'loop' => __('matinee::oembed.loop'),
            'show_title' => __('matinee::oembed.title'),
            'byline' => __('matinee::oembed.byline'),
            'portrait' => __('matinee::oembed.portrait'),
        ];
    }

    public function getAdditionalFields(): array
    {
        return [];
    }
}
