<?php

declare(strict_types=1);

namespace Awcodes\Matinee;

use Awcodes\Matinee\Providers\Contracts\MatineeProvider;
use Awcodes\Matinee\Providers\VimeoProvider;
use Awcodes\Matinee\Providers\YoutubeProvider;
use Closure;
use Exception;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Component as FormsComponent;
use Filament\Schemas\Components\Concerns\HasLabel;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Support\Concerns\CanSpanColumns;
use Illuminate\Contracts\Support\Htmlable;
use Livewire\Component;

class Matinee extends FormsComponent
{
    use CanSpanColumns;
    use HasLabel;

    protected ?array $providers = null;

    protected ?string $provider = null;

    protected string $view = 'matinee::matinee';

    protected string $name;

    protected bool|Closure|null $isRequired = null;

    protected bool|Closure|null $shouldShowPreview = null;

    final public function __construct(?string $name = null)
    {
        $this->name($name);
    }

    /** @throws Exception */
    protected function setUp(): void
    {
        $this
            ->statePath($this->getName())
            ->schema([
                TextInput::make('url')
                    ->label(trans('matinee::matinee.url'))
                    ->live(debounce: 500)
                    ->required(fn (): bool => $this->isRequired())
                    ->rules([
                        fn (): Closure => function (string $attribute, $value, Closure $fail): void {
                            if ($value && ! $this->getProvider($value) instanceof MatineeProvider) {
                                $fail(trans('matinee::matinee.invalid_url'));
                            }
                        },
                    ])
                    ->afterStateUpdated(function (TextInput $component, Component $livewire, Set $set, Get $get, $state): void {
                        if (! $state) {
                            $set('embed_url', null);
                        }

                        $livewire->validateOnly($component->getStatePath());

                        $provider = $this->getProvider($state);
                        $set('embed_url', $provider?->convertUrl($get('options')) ?? null);
                        $set('options', $provider?->getOptions() ?? []);
                    }),
                Group::make([
                    Group::make([
                        Hidden::make('embed_url')
                            ->dehydrateStateUsing(function (Set $set, Get $get): ?string {
                                if ($get('url')) {
                                    return $this
                                        ->getProvider($get('url'))
                                        ->convertUrl($get('options'));
                                }

                                return null;
                            }),
                        Group::make([
                            TextInput::make('width')
                                ->required(fn (): bool => $this->isRequired())
                                ->label(trans('matinee::matinee.width'))
                                ->afterStateHydrated(fn (TextInput $component, $state): TextInput => $component->state($state ?: '16'))
                                ->suffix(fn (Get $get): string => $get('responsive') ? '%' : 'px'),
                            TextInput::make('height')
                                ->required(fn (): bool => $this->isRequired())
                                ->label(trans('matinee::matinee.height'))
                                ->afterStateHydrated(fn (TextInput $component, $state): TextInput => $component->state($state ?: '9'))
                                ->suffix(fn (Get $get): string => $get('responsive') ? '%' : 'px'),
                        ])->columns(),
                        Toggle::make('responsive')
                            ->live()
                            ->label(trans('matinee::matinee.responsive'))
                            ->afterStateHydrated(fn (Toggle $component, $state): Toggle => $component->state($state ?: true))
                            ->afterStateUpdated(function (Set $set, $state): void {
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
                        ->live(onBlur: true)
                        ->label(trans('matinee::matinee.options'))
                        ->afterStateUpdated(function (Set $set, Get $get, $state): void {
                            $provider = $this->getProvider($get('url'));
                            $set('embed_url', $provider?->convertUrl($get('options')) ?? null);
                        }),
                ])->columns(),
            ]);
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

    public function getLabel(): string|Htmlable|null
    {
        $label = $this->evaluate($this->name);
        $label = ($this->shouldTranslateLabel)
            ? __($label)
            : $label;

        return (string) str($label)->snake(' ')->replace('_', ' ')->title();
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

            return collect($domains)->mapWithKeys(fn ($domain) => [$domain => $provider]);
        })->toArray();
    }

    public function getProvider(?string $url): ?MatineeProvider
    {
        $providers = $this->getProviders();
        $providerId = 'youtube';

        $domain = parse_url((str_contains((string) $url, '://') ? '' : 'http://').trim((string) $url), PHP_URL_HOST);

        if (preg_match('/[a-z0-9][a-z0-9\-]{0,63}\.[a-z]{2,6}(\.[a-z]{1,2})?$/i', $domain, $match)) {
            $providerId = $match[0];
        }

        return array_key_exists($providerId, $providers)
            ? $providers[$providerId]::make()->url($url)
            : null;
    }

    public function showPreview(bool|Closure $showPreview = true): static
    {
        $this->shouldShowPreview = $showPreview;

        return $this;
    }

    public function shouldShowPreview(): bool
    {
        return $this->evaluate($this->shouldShowPreview) ?? false;
    }

    public function required(bool|Closure $required = true): static
    {
        $this->isRequired = $required;

        return $this;
    }

    public function isRequired(): bool
    {
        return $this->evaluate($this->isRequired) ?? false;
    }
}
