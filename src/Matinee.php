<?php

namespace Awcodes\Matinee;

use Awcodes\Matinee\Providers\Contracts\MatineeProvider;
use Awcodes\Matinee\Providers\VimeoProvider;
use Awcodes\Matinee\Providers\YoutubeProvider;
use Closure;
use Filament\Forms\Components\Component as FormsComponent;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Contracts\Support\Htmlable;
use Livewire\Component;

class Matinee extends FormsComponent
{
    protected ?array $providers = null;

    protected ?string $provider = null;

    protected string $view = 'matinee::matinee';

    protected string $name;

    protected bool | Closure | null $isRequired = null;

    protected bool | Closure | null $shouldShowPreview = null;

    final public function __construct(?string $name = null)
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

    public function getLabel(): string | Htmlable | null
    {
        $label = $this->evaluate($this->name);
        $label = (is_string($label) && $this->shouldTranslateLabel) ?
            __($label) :
            $label;

        return (string) str($label)->snake(' ')->replace('_', ' ')->title();
    }

    protected function setUp(): void
    {
        $this
            ->statePath($this->getName())
            ->schema([
                TextInput::make('url')
                    ->label(trans('matinee::matinee.url'))
                    ->live(debounce: 500)
                    ->required(fn () => $this->isRequired())
                    ->rules([
                        function () {
                            return function (string $attribute, $value, Closure $fail) {
                                if ($value && ! $this->getProvider($value)) {
                                    $fail(trans('matinee::matinee.invalid_url'));
                                }
                            };
                        },
                    ])
                    ->afterStateUpdated(function (TextInput $component, Component $livewire, Set $set, Get $get, $state) {
                        if (! $state) {
                            $set('embed_url', null);

                            return;
                        }

                        $livewire->validateOnly($component->getStatePath());

                        $provider = $this->getProvider($state);
                        $set('embed_url', $provider->convertUrl($get('options')));
                        $set('options', $provider->getOptions());
                    }),
                Group::make([
                    Group::make([
                        Hidden::make('embed_url')
                            ->dehydrateStateUsing(function (Set $set, Get $get) {
                                if ($get('url')) {
                                    return $this
                                        ->getProvider($get('url'))
                                        ->convertUrl($get('options'));
                                }

                                return null;
                            }),
                        Group::make([
                            TextInput::make('width')
                                ->required(fn () => $this->isRequired())
                                ->label(trans('matinee::matinee.width'))
                                ->afterStateHydrated(fn (TextInput $component, $state) => $component->state($state ?: '16'))
                                ->suffix(function (Get $get) {
                                    return $get('responsive') ? '%' : 'px';
                                }),
                            TextInput::make('height')
                                ->required(fn () => $this->isRequired())
                                ->label(trans('matinee::matinee.height'))
                                ->afterStateHydrated(fn (TextInput $component, $state) => $component->state($state ?: '9'))
                                ->suffix(function (Get $get) {
                                    return $get('responsive') ? '%' : 'px';
                                }),
                        ])->columns(),
                        Toggle::make('responsive')
                            ->live()
                            ->label(trans('matinee::matinee.responsive'))
                            ->afterStateHydrated(function (Toggle $component, $state) {
                                return $component->state($state ?: true);
                            })
                            ->afterStateUpdated(function (Set $set, $state) {
                                if ($state) {
                                    $set('width', '16');
                                    $set('height', '9');
                                } else {
                                    $set('width', '640');
                                    $set('height', '480');
                                }
                            }),
                    ]),
                    KeyValue::make('options')
                        ->label(trans('matinee::matinee.options')),
                ])->columns(),
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
            ...[VimeoProvider::class, YoutubeProvider::class],
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

    public function showPreview(bool | Closure $showPreview = true): static
    {
        $this->shouldShowPreview = $showPreview;

        return $this;
    }

    public function shouldShowPreview(): bool
    {
        return $this->evaluate($this->shouldShowPreview) ?? false;
    }

    public function required(bool | Closure $required = true): static
    {
        $this->isRequired = $required;

        return $this;
    }

    public function isRequired(): bool
    {
        return $this->evaluate($this->isRequired) ?? false;
    }
}
