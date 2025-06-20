<?php

declare(strict_types=1);

namespace Awcodes\Matinee\Providers;

use Awcodes\Matinee\Providers\Concerns\IsMatineeProvider;
use Illuminate\Support\Str;

class VimeoProvider implements Contracts\MatineeProvider
{
    use IsMatineeProvider;

    public function getDomains(): array
    {
        return [
            'vimeo.com',
        ];
    }

    public function getOptions(): array
    {
        return [
            'autoplay' => 0,
            'loop' => 0,
            'show_title' => 0,
            'byline' => 0,
            'portrait' => 0,
        ];
    }

    public function convertUrl(?array $options = []): string
    {
        $baseUrl = 'https://player.vimeo.com/video/';

        if (Str::of($this->url)->contains('/video/')) {
            preg_match('/(\d+)/', $this->url, $matches);
        } else {
            preg_match('/\.com\/(\d+)/', $this->url, $matches);
        }

        $id = $matches[1] ?? null;

        $baseUrl .= $id;

        if (filled($options)) {
            $query = http_build_query($options);

            return "$baseUrl?$query";
        }

        return $baseUrl;
    }
}
