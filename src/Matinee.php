<?php

namespace Awcodes\Matinee;

use Awcodes\Matinee\Providers\VimeoProvider;
use Awcodes\Matinee\Providers\YoutubeProvider;
use Filament\Forms;

class Matinee extends Forms\Components\Fieldset
{
    protected ?array $providers = null;

    protected function setUp(): void
    {
        $this
            ->label($this->getLabel())
            ->schema([
                Forms\Components\Group::make([
                    Forms\Components\Hidden::make('embed_url'),
                    Forms\Components\TextInput::make('url')
                        ->label(__('matinee::matinee.url'))
                        ->live(debounce: 500)
                        ->required()
                        ->columnSpanFull()
                        ->afterStateUpdated(function (Forms\Set $set, Forms\Get $get, $state) {
                            if (! $state) {
                                $set('embed_url', null);
                            }

                            $set('embed_url', $this->convertUrl($get('url')));
                        }),
                    ...$this->getProvidersSchema(),
                    Forms\Components\Checkbox::make('responsive')
                        ->reactive()
                        ->label(__('matinee::matinee.responsive'))
                        ->afterStateHydrated(fn ($component, $state) => $component->state($state ?: true))
                        ->afterStateUpdated(function (Forms\Set $set, $state) {
                            if ($state) {
                                $set('width', '16');
                                $set('height', '9');
                            } else {
                                $set('width', '640');
                                $set('height', '480');
                            }
                        })
                        ->columnSpan('full'),
                    Forms\Components\TextInput::make('width')
                        ->required()
                        ->label(__('matinee::matinee.width'))
                        ->afterStateHydrated(fn ($component, $state) => $component->state($state ?: '16')),
                    Forms\Components\TextInput::make('height')
                        ->required()
                        ->label(__('matinee::matinee.height'))
                        ->afterStateHydrated(fn ($component, $state) => $component->state($state ?: '9')),
                ])->columns(['md' => 2]),
                Forms\Components\ViewField::make('preview')
                    ->view('matinee::preview')
                    ->label(fn(): string => __('matinee::matinee.preview')),
            ])->columns(['md' => 2]);
    }

    public function providers(array $providers): self
    {
        $this->providers = $providers;

        return $this;
    }

    public function getProviders(): array
    {
        return collect([
            ...$this->providers ?? [],
            ...[VimeoProvider::class, YoutubeProvider::class]
        ])->mapWithKeys(function ($provider) {
            return [$provider::make()->getId() => $provider];
        })->toArray();
    }

    public function getProvidersSchema(): array
    {
        $providerOptions = [];

        foreach ($this->getProviders() as $provider) {
            $provider = $provider::make();

            $providerOptions[] = Forms\Components\Group::make([
                Forms\Components\CheckboxList::make('params')
                    ->hiddenLabel()
                    ->gridDirection('row')
                    ->columns(3)
                    ->live()
                    ->options(function () use ($provider) {
                        return $provider->getOptions();
                    }),
                ...$provider->getAdditionalFields(),
            ])->statePath('options')
                ->visible(function (Forms\Get $get) use ($provider) {
                return $provider->shouldShow($get('url'));
            })->columnSpanFull();
        }

        return $providerOptions;
    }

    public function convertUrl(string $url, array $options = []): string
    {
        $provider = collect($this->getProviders())
            ->filter(fn ($provider) => $provider::make()->shouldShow($url))
            ->sole();

        if ($provider) {
            return $provider::make()->convertUrl($url, $options);
        }

        return $url;
    }
}
