<?php

namespace Awcodes\Matinee\Providers\Concerns;

use Filament\Forms\Components\Concerns\HasId;

trait IsProvider
{
    use HasId;

    public static function make(): static
    {
        return new static();
    }
}
