<?php

namespace Awcodes\Matinee\Providers\Concerns;

use Filament\Forms\Components\Concerns\HasId;

trait IsMatineeProvider
{
    use HasId;

    protected string $url;

    public static function make(): static
    {
        return new static();
    }

    public function url(string $url): static
    {
        $this->url = $url;

        return $this;
    }
}
