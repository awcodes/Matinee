<?php

namespace Awcodes\Matinee\Providers;

use Illuminate\Support\Str;

class NativeProvider implements Contracts\MatineeProvider
{
    use Concerns\IsProvider;

    public function getId(): ?string
    {
        return 'native';
    }

    public function shouldShow(?string $url): bool
    {
        if (! $url) {
            return false;
        }

        return ! Str::of($url)->contains(['vimeo', 'youtube', 'youtu.be']);
    }

    public function getOptions(): array
    {
        return [
            'autoplay' => __('matinee::matinee.autoplay'),
            'loop' => __('matinee::matinee.loop'),
            'controls' => __('matinee::matinee.controls'),
        ];
    }

    public function getAdditionalFields(): array
    {
        return [];
    }

    public function convertUrl(string $url, array $options = []): string
    {
        return $url;
    }
}
