<?php

namespace Awcodes\Matinee\Providers;

use Carbon\Carbon;
use Carbon\CarbonInterval;
use Closure;
use Filament\Forms;
use Illuminate\Support\Str;

class YoutubeProvider implements Contracts\MatineeProvider
{
    use Concerns\IsProvider;

    protected string | Closure | null $id = 'youtube';

    public function shouldShow(string $url): bool
    {
        return Str::of($url)->contains(['youtube', 'youtu.be']);
    }

    public function getOptions(): array
    {
        return [
            'controls' => __('matinee::oembed.controls'),
            'nocookie' => __('matinee::oembed.nocookie'),
        ];
    }

    public function getAdditionalFields(): array
    {
        return [
            Forms\Components\TimePicker::make('start_at')
                ->label(__('matinee::oembed.start_at'))
                ->reactive()
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
}
