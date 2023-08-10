<?php

namespace Awcodes\Matinee\Providers;

use Illuminate\Support\Str;

class VimeoProvider implements Contracts\MatineeProvider
{
    use Concerns\IsProvider;

    public function getId(): ?string
    {
        return 'vimeo';
    }

    public function shouldShow(?string $url): bool
    {
        if (! $url) {
            return false;
        }

        return str_contains($url, 'vimeo');
    }

    public function getOptions(): array
    {
        return [
            'autoplay' => __('matinee::matinee.autoplay'),
            'loop' => __('matinee::matinee.loop'),
            'show_title' => __('matinee::matinee.title'),
            'byline' => __('matinee::matinee.byline'),
            'portrait' => __('matinee::matinee.portrait'),
        ];
    }

    public function getAdditionalFields(): array
    {
        return [];
    }

    public function convertUrl(string $url, array $options = []): string
    {
        if (Str::of($url)->contains('/video/')) {
            return $url;
        }

        preg_match('/\.com\/([0-9]+)/', $url, $matches);

        if (! $matches || ! $matches[1]) {
            return '';
        }

        $query = http_build_query([
            'autoplay' => $options['autoplay'] ?? false,
            'loop' => $options['loop'] ?? false,
            'title' => $options['show_title'] ?? false,
            'byline' => $options['byline'] ?? false,
            'portrait' => $options['portrait'] ?? false,
        ]);

        return "https://player.vimeo.com/video/{$matches[1]}?{$query}";
    }
}
