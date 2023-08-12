<?php

namespace Awcodes\Matinee;

use Awcodes\Matinee\Providers\Contracts\MatineeProvider;
use Awcodes\Matinee\Providers\VimeoProvider;
use Awcodes\Matinee\Providers\YoutubeProvider;
use Closure;
use Filament\Forms;
use Livewire\Component;

class Matinee extends Forms\Components\Component
{
    protected ?array $providers = null;

    protected ?string $provider = null;

    protected string $view = 'matinee::matinee';

    protected string $name;

    final public function __construct(string $name = null)
    {
        $this->name($name);
    }

    public static function make(string $name): static
    {
        $static = app(static::class, ['name' => $name]);
        $static->configure();

        return $static;
    }

    public function name(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    protected function setUp(): void
    {
        $this
            ->label($this->getLabel())
            ->schema([
                Forms\Components\Hidden::make($this->name . '.embed_url')
                    ->dehydrateStateUsing(function (Forms\Set $set, Forms\Get $get) {
                        return $this->getProvider($get($this->name . '.url'))->convertUrl($get($this->name . '.options'));
                    }),
                Forms\Components\TextInput::make($this->name . '.url')
                    ->label(__('matinee::matinee.url'))
                    ->live(debounce: 500)
                    ->required()
                    ->rules([
                        function () {
                            return function (string $attribute, $value, Closure $fail) {
                                if (! $this->getProvider($value)) {
                                    $fail(__('matinee::matinee.invalid_url'));
                                }
                            };
                        },
                    ])
                    ->afterStateUpdated(function (Forms\Components\TextInput $component, Component $livewire, Forms\Set $set, Forms\Get $get, $state) {
                        if (! $state) {
                            $set($this->name . '.embed_url', null);
                        }

                        $livewire->validateOnly($component->getStatePath());

                        $provider = $this->getProvider($state);
                        $set($this->name . '.embed_url', $provider->convertUrl($get($this->name . '.options')));
                        $set($this->name . '.options', $provider->getOptions());
                    }),
                Forms\Components\KeyValue::make($this->name . '.options')
                    ->label(__('matinee::matinee.options')),
                Forms\Components\Toggle::make($this->name . '.responsive')
                    ->reactive()
                    ->label(__('matinee::matinee.responsive'))
                    ->afterStateHydrated(fn ($component, $state) => $component->state($state ?: true))
                    ->afterStateUpdated(function (Forms\Set $set, $state) {
                        if ($state) {
                            $set($this->name . '.width', '16');
                            $set($this->name . '.height', '9');
                        } else {
                            $set($this->name . '.width', '640');
                            $set($this->name . '.height', '480');
                        }
                    }),
                Forms\Components\Group::make([
                    Forms\Components\TextInput::make($this->name . '.width')
                        ->required()
                        ->label(__('matinee::matinee.width'))
                        ->afterStateHydrated(fn ($component, $state) => $component->state($state ?: '16')),
                    Forms\Components\TextInput::make($this->name . '.height')
                        ->required()
                        ->label(__('matinee::matinee.height'))
                        ->afterStateHydrated(fn ($component, $state) => $component->state($state ?: '9')),
                ])
                ->columns()
            ]);
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
            $instance = $provider::make();
            $domains = $instance->getDomains();

            if (empty($domains)) {
                return [$instance->getId() => $provider];
            }

            return collect($domains)->mapWithKeys(function ($domain) use ($provider) {
                return [$domain => $provider];
            });
        })->toArray();
    }

    public function getProvider(string $url): ?MatineeProvider
    {
        $providers = $this->getProviders();
        $providerId = 'youtube';

        $domain = parse_url((! str_contains($url, '://') ? 'http://' : '') . trim($url), PHP_URL_HOST);

        if (preg_match('/[a-z0-9][a-z0-9\-]{0,63}\.[a-z]{2,6}(\.[a-z]{1,2})?$/i', $domain, $match)) {
            $providerId = $match[0];
        }

        return array_key_exists($providerId, $providers)
            ? $providers[$providerId]::make()->url($url)
            : null;
    }
}
