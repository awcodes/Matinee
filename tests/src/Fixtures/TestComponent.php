<?php

declare(strict_types=1);

namespace Awcodes\Matinee\Tests\Fixtures;

use Awcodes\Matinee\Matinee;
use Exception;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TestComponent extends TestForm
{
    /** @throws Exception */
    public function form(Schema $form): Schema
    {
        return $form
            ->statePath('data')
            ->schema([
                TextInput::make('title'),
                TextInput::make('slug'),
                Matinee::make('video'),
            ]);
    }
}
