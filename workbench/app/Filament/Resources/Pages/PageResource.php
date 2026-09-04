<?php

declare(strict_types=1);

namespace Workbench\App\Filament\Resources\Pages;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Workbench\App\Filament\Resources\Pages\Pages\CreatePage;
use Workbench\App\Filament\Resources\Pages\Pages\EditPage;
use Workbench\App\Filament\Resources\Pages\Pages\ListPages;
use Workbench\App\Filament\Resources\Pages\Schemas\PageForm;
use Workbench\App\Models\Page;

class PageResource extends Resource
{
    protected static ?string $model = Page::class;

    public static function form(Schema $schema): Schema
    {
        return PageForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->searchable(),
                TextColumn::make('slug'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPages::route('/'),
            'create' => CreatePage::route('/create'),
            'edit' => EditPage::route('/{record}/edit'),
        ];
    }
}
