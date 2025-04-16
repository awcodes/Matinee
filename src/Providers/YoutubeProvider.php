<?php

namespace Awcodes\Matinee\Providers;

use Awcodes\Matinee\Providers\Concerns\IsMatineeProvider;
use Carbon\Carbon;
use Illuminate\Support\Str;

class YoutubeProvider implements Contracts\MatineeProvider
{
    use IsMatineeProvider;

    public function getId(): ?string
    {
        return 'youtube';
    }

    public function getDomains(): array
    {
        return [
            'youtube.com',
            'youtu.be',
        ];
    }

    public function getOptions(): array
    {
        return [
            'controls' => 1,
            'nocookie' => 0,
            'start' => '00:00:00',
        ];
    }

    public function convertUrl(?array $options = []): string
    {
        $baseUrl = isset($options['nocookie']) && ($options['nocookie'] === '1' || $options['nocookie'] === 'true')
            ? 'https://www.youtube-nocookie.com/embed/'
            : 'https://www.youtube.com/embed/';

        unset($options['nocookie']);

        if (Str::of($this->url)->contains('youtu.be')) {
            $id = Str::of($this->url)->after('youtu.be/');
        } elseif (Str::of($this->url)->contains('youtube.com/shorts/')) {
            $id = Str::of($this->url)->after('youtube.com/shorts/');
        } else {
            preg_match('/v=([-\w]+)/', $this->url, $matches);
            $id = $matches[1] ?? null;
        }

        $baseUrl = $baseUrl . $id;

        if (isset($options['start'])) {
            $options['start'] = Str::of($options['start'])->isMatch('/([0-9]{2}):([0-9]{2}):([0-9]{2})/')
                ? Carbon::parse('00:00:00')->diffInSeconds($options['start'])
                : null;
        }

        if (filled($options)) {
            $query = http_build_query($options);

            return "{$baseUrl}?{$query}";
        }

        return $baseUrl;
    }
}
