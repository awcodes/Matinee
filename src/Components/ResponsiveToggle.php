<?php

declare(strict_types=1);

namespace Awcodes\Matinee\Components;

use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\StateCasts\BooleanStateCast;

/**
 * Filament's toggle casts a missing value to false before hydration hooks run, which would make an embed with no
 * stored `responsive` value indistinguishable from one stored as false. Keeping null lets the field default it to
 * true while a stored false stays false.
 *
 * @internal
 */
class ResponsiveToggle extends Toggle
{
    public function getDefaultStateCasts(): array
    {
        return [app(BooleanStateCast::class, ['isNullable' => true])];
    }
}
