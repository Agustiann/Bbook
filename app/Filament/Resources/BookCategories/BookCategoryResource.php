<?php

namespace App\Filament\Resources\BookCategories;

use App\Filament\Resources\BookCategories\Pages\CreateBookCategory;
use App\Filament\Resources\BookCategories\Pages\EditBookCategory;
use App\Filament\Resources\BookCategories\Pages\ListBookCategories;
use App\Filament\Resources\BookCategories\Schemas\BookCategoryForm;
use App\Filament\Resources\BookCategories\Tables\BookCategoriesTable;
use App\Models\BookCategory;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class BookCategoryResource extends Resource
{
    protected static ?string $model = BookCategory::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBookmarkSquare;

    protected static ?string $recordTitleAttribute = 'category_name';

    public static function getNavigationGroup(): ?string
    {
        return 'Master Data';
    }

    public static function form(Schema $schema): Schema
    {
        return BookCategoryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BookCategoriesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBookCategories::route('/'),
            'create' => CreateBookCategory::route('/create'),
            'edit' => EditBookCategory::route('/{record}/edit'),
        ];
    }
}
