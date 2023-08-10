<?php

namespace Awcodes\Matinee\Providers;

use Carbon\Carbon;
use Carbon\CarbonInterval;
use Filament\Forms;
use Illuminate\Support\Str;

class YoutubeProvider implements Contracts\MatineeProvider
{
    use Concerns\IsProvider;

    public function getId(): ?string
    {
        return 'youtube';
    }

    public function shouldShow(?string $url): bool
    {
        if (! $url) {
            return false;
        }

        return Str::of($url)->contains(['youtube', 'youtu.be']);
    }

    public function getOptions(): array
    {
        return [
            'controls' => __('matinee::matinee.controls'),
            'nocookie' => __('matinee::matinee.nocookie'),
        ];
    }

    public function getAdditionalFields(): array
    {
        return [
            Forms\Components\TimePicker::make('start_at')
                ->label(__('matinee::matinee.start_at'))
                ->live()
                ->date(false)
                ->afterStateHydrated(function (Forms\Components\TimePicker $component, $state): void {
                    if (! $state) {
                        return;
                    }

                    $state = CarbonInterval::seconds($state)->cascade();
                    $component->state(Carbon::parse($state->h . ':' . $state->i . ':' . $state->s)->format('Y-m-d H:i:s'));
                })
                ->dehydrateStateUsing(function ($state): int {
                    if (! $state) {
                        return 0;
                    }

                    return Carbon::parse($state)->diffInSeconds('00:00:00');
                }),
        ];
    }

    public function convertUrl(string $url, array $options = []): string
    {
        if (Str::of($url)->contains('/embed/')) {
            return $url;
        }

        if (Str::of($url)->contains('youtu.be')) {
            $id = Str::of($url)->after('youtu.be/');

            if (! $id) {
                return '';
            }

            return filled($options['params']) && $options['nocookie']
                ? 'https://www.youtube-nocookie.com/embed/' . $id
                : 'https://www.youtube.com/embed/' . $id;
        }

        preg_match('/v=([-\w]+)/', $url, $matches);

        if (! $matches || ! $matches[1]) {
            return '';
        }

        if (filled($options)) {

        $params = [
            'controls' => isset($options['params']['controls']) ?? 0,
        ];

        if ($options['start_at']) {
            $params['start'] = $options['start_at'];
        }

        $query = http_build_query($params);

        return "https://www.youtube.com/embed/{$matches[1]}?{$query}";
    }
}
