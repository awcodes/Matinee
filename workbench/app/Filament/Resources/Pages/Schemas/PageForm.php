<?php

declare(strict_types=1);

namespace Workbench\App\Filament\Resources\Pages\Schemas;

use Awcodes\Matinee\Matinee;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')->required(),
                TextInput::make('slug')->required(),
                Matinee::make('video')
                    ->showPreview()
                    ->columnSpanFull(),
            ]);
    }
}
