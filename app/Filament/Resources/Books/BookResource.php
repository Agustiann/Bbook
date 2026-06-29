<?php

namespace App\Filament\Resources\Books;

use App\Filament\Resources\Books\Pages\CreateBook;
use App\Filament\Resources\Books\Pages\EditBook;
use App\Filament\Resources\Books\Pages\ListBooks;
use App\Filament\Resources\Books\Pages\ViewBook;
use App\Filament\Resources\Books\Schemas\BookForm;
use App\Filament\Resources\Books\Tables\BooksTable;
use App\Filament\Resources\Books\Tables\BookCatalogTable;
use App\Models\Book;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class BookResource extends Resource
{
    protected static ?string $model = Book::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBookOpen;
    protected static ?string $recordTitleAttribute = 'title';

    public static function isBorrower(): bool
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        return $user?->hasRole('Borrower') ?? false;
    }

    public static function getNavigationLabel(): string
    {
        return static::isBorrower() ? 'Book Catalog' : 'Books';
    }

    public static function getNavigationGroup(): ?string
    {
        return static::isBorrower() ? null : 'Master Data';
    }
    public static function form(Schema $schema): Schema
    {
        return BookForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return static::isBorrower()
            ? BookCatalogTable::configure($table)
            : BooksTable::configure($table);
    }

    public static function getPages(): array
    {
        if (static::isBorrower()) {
            return [
                'index' => ListBooks::route('/'),
            ];
        }
        return [
            'index' => ListBooks::route('/'),
            'create' => CreateBook::route('/create'),
            'view' => ViewBook::route('/{record}'),
            'edit' => EditBook::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getRelations(): array
    {
        return static::isBorrower()
            ? []
            : [RelationManagers\TransactionsRelationManager::class];
    }
}