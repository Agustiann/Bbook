<?php

namespace App\Filament\Resources\BookCatalogs\Tables;

use App\Models\Book;
use App\Models\Transaction;
use Filament\Actions\Action;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class BookCatalogsTable
{
    protected static function borrower(): ?\App\Models\Borrower
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        if (! $user?->hasRole('Borrower')) {
            return null;
        }

        return $user->borrower;
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
                    ->visible(function (Book $record) {

                        $borrower = self::borrower();

                        if (! $borrower) {
                            return false;
                        }

                        $alreadyBorrowed = Transaction::query()
                            ->where('borrower_id', $borrower->id)
                            ->where('book_id', $record->id)
                            ->whereIn('status', ['borrowed', 'extended'])
                            ->exists();

                        return ! $alreadyBorrowed
                            && $record->stock > 0;
                    })
                    ->action(function (Book $record, $livewire) {
                        $livewire->borrow($record->id);
                    }),

                Action::make('alreadyBorrowed')
                    ->label('Already Borrowed')
                    ->disabled()
                    ->color('gray')
                    ->visible(function (Book $record) {

                        $borrower = self::borrower();

                        if (! $borrower) {
                            return false;
                        }

                        return Transaction::query()
                            ->where('borrower_id', $borrower->id)
                            ->where('book_id', $record->id)
                            ->whereIn('status', ['borrowed', 'extended'])
                            ->exists();
                    }),

                Action::make('outOfStock')
                    ->label('Out of Stock')
                    ->disabled()
                    ->color('danger')
                    ->visible(function (Book $record) {

                        $borrower = self::borrower();

                        if (! $borrower) {
                            return false;
                        }
                        $alreadyBorrowed = Transaction::query()
                            ->where('borrower_id', $borrower->id)
                            ->where('book_id', $record->id)
                            ->whereIn('status', ['borrowed', 'extended'])
                            ->exists();

                        return ! $alreadyBorrowed
                            && $record->stock <= 0;
                    }),
            ]);
    }
}
