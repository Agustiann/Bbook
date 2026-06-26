<?php

namespace App\Filament\Resources\BookCatalogs;

use App\Filament\Resources\BookCatalogs\Tables\BookCatalogsTable;
use App\Filament\Resources\BookCatalogs\Pages\ListBookCatalogs;
use App\Models\Book;
use Filament\Resources\Resource;
use Filament\Tables\Table;

class BookCatalogResource extends Resource
{
    protected static ?string $model = Book::class;
    protected static ?string $modelLabel = 'Book Catalog';
    protected static ?string $pluralModelLabel = 'Book Catalog';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-book-open';

    protected static ?string $navigationLabel = 'Book Catalog';

    public static function table(Table $table): Table
    {
        return BookCatalogsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBookCatalogs::route('/'),
        ];
    }
}
