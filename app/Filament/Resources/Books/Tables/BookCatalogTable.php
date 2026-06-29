<?php

namespace App\Filament\Resources\Books\Tables;

use App\Models\Book;
use App\Models\Transaction;
use Filament\Actions\Action;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class BookCatalogTable
{
    protected static function status(Book $record): string
    {
        $borrower = Auth::user()->borrower;

        $alreadyBorrowed = Transaction::query()
            ->where('borrower_id', $borrower->id)
            ->where('book_id', $record->id)
            ->whereIn('status', ['borrowed', 'extended'])
            ->exists();

        return match (true) {
            $alreadyBorrowed => 'already_borrowed',
            $record->stock <= 0 => 'out_of_stock',
            default => 'available',
        };
    }

    public static function configure(Table $table): Table
    {
        return $table
            ->query(
                Book::query()->with('category')
            )
            ->contentGrid([
                'md' => 2,
                'xl' => 3,
            ])
            ->columns([
                Split::make([
                    ViewColumn::make('photo')
                        ->view('filament.tables.columns.book-photo'),

                    Stack::make([
                        TextColumn::make('title')
                            ->weight('bold')
                            ->size('lg'),

                        TextColumn::make('author'),

                        TextColumn::make('category.category_name'),

                        TextColumn::make('stock')
                            ->badge()
                            ->color(fn($state) => $state > 0 ? 'success' : 'danger')
                            ->formatStateUsing(fn($state) => "Stock : {$state}"),

                        TextColumn::make('availability')
                            ->state(fn(Book $record) => $record->stock > 0 ? 'Available' : 'Out of Stock')
                            ->badge()
                            ->color(fn(Book $record) => $record->stock > 0 ? 'success' : 'danger'),
                    ]),
                ]),
            ])
            ->recordActions([
                Action::make('borrow')
                    ->label('Borrow')
                    ->icon('heroicon-o-book-open')
                    ->color('primary')
                    ->visible(fn(Book $record) => self::status($record) === 'available')
                    ->action(fn(Book $record, $livewire) => $livewire->borrow($record->id)),

                Action::make('alreadyBorrowed')
                    ->label('Already Borrowed')
                    ->disabled()
                    ->color('gray')
                    ->visible(fn(Book $record) => self::status($record) === 'already_borrowed'),

                Action::make('outOfStock')
                    ->label('Out of Stock')
                    ->disabled()
                    ->color('danger')
                    ->visible(fn(Book $record) => self::status($record) === 'out_of_stock'),
            ]);
    }
}