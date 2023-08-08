<?php

namespace Awcodes\Matinee\Providers;

use Closure;
use Illuminate\Support\Str;

class NativeProvider implements Contracts\MatineeProvider
{
    use Concerns\IsProvider;

    protected string | Closure | null $id = 'native';

    public function shouldShow(string $url): bool
    {
        return ! Str::of($url)->contains(['vimeo', 'youtube', 'youtu.be']);
    }

    public function getOptions(): array
    {
        return [
            'autoplay' => __('matinee::oembed.autoplay'),
            'loop' => __('matinee::oembed.loop'),
            'controls' => __('matinee::oembed.controls'),
        ];
    }

    public function getAdditionalFields(): array
    {
        return [];
    }
}
