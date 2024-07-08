<?php

namespace Awcodes\Matinee\Providers;

use Awcodes\Matinee\Providers\Concerns\IsMatineeProvider;
use Illuminate\Support\Str;

class TiktokProvider implements Contracts\MatineeProvider
{
    use IsMatineeProvider;

    public function getDomains(): array
    {
        return [
            'tiktok.com',
        ];
    }

    public function getOptions(): array
    {
        return [
            'controls' => 1,
            'loop' => 0,
            'progress_bar' => 1,
            'volume_control' => 1,
            'fullscreen_button' => 1,
        ];
    }

    public function convertUrl(?array $options = []): string
    {
        $baseUrl = 'https://www.tiktok.com/player/v1/';

        if (Str::of($this->url)->contains('/video/')) {
            preg_match('/([0-9]+)/', $this->url, $matches);
            $id = $matches[1] ?? null;
        } else {
            preg_match('/\.com\/([0-9]+)/', $this->url, $matches);
            $id = $matches[1] ?? null;
        }

        $baseUrl = $baseUrl . $id;

        if (filled($options)) {
            $query = http_build_query($options);

            return "{$baseUrl}?{$query}";
        }

        return $baseUrl;
    }
}
