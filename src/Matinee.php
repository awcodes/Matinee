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
        $this->schema([
            Forms\Components\TextInput::make('url')
                ->label(__('matinee::oembed.url'))
                ->reactive()
                ->required(),
            ...$this->getProvidersSchema(),
            Forms\Components\Checkbox::make('responsive')
                ->default(true)
                ->reactive()
                ->label(__('matinee::oembed.responsive'))
                ->afterStateUpdated(function (callable $set, $state) {
                    if ($state) {
                        $set('width', '16');
                        $set('height', '9');
                    } else {
                        $set('width', '640');
                        $set('height', '480');
                    }
                })
                ->columnSpan('full'),
            Forms\Components\Group::make([
                Forms\Components\TextInput::make('width')
                    ->reactive()
                    ->required()
                    ->label(__('matinee::oembed.width'))
                    ->default('16'),
                Forms\Components\TextInput::make('height')
                    ->reactive()
                    ->required()
                    ->label(__('matinee::oembed.height'))
                    ->default('9'),
            ])->columns(['md' => 2]),
            Forms\Components\ViewField::make('preview')
                ->view('matinee::forms.oembed.preview')
                ->label(fn (): string => __('matinee::oembed.preview'))
                ->columnSpan('full')
                ->dehydrated(false),
        ]);
    }

    public function providers(array $providers): self
    {
        $this->providers = $providers;

        return $this;
    }

    public function getProviders(): array
    {
        return [
            ...$this->providers ?? [],
            ...[VimeoProvider::class, YoutubeProvider::class]
        ];
    }

    public function getProvidersSchema(): array
    {
        $providerOptions = [];

        foreach ($this->getProviders() as $provider) {
            $provider = $provider::make();

            $providerOptions[] = Forms\Components\Group::make([
                Forms\Components\CheckboxList::make($provider->getId() . '_options')
                    ->hiddenLabel()
                    ->gridDirection('row')
                    ->columns(3)
                    ->options(function () use ($provider) {
                        return $provider->getOptions();
                    }),
                ...$provider->getAddtionalFields(),
            ])->visible(function (Forms\Get $get) use ($provider) {
                return $provider->shouldShow($get('url'));
            });
        }

        return $providerOptions;
    }
}
