<?php

namespace App\Filament\Resources\Transactions\Schemas;

use App\Models\Book;
use App\Models\Transaction;
use App\Models\User;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TransactionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Radio::make('borrower_type')
                    ->label('Borrower Type')
                    ->options([
                        'new' => 'New Borrower',
                        'existing' => 'Existing Borrower',
                    ])
                    ->default('existing')
                    ->live()
                    ->required(),

                Select::make('borrower_id')
                    ->label('Borrower')
                    ->relationship('borrower', 'name')
                    ->searchable()
                    ->preload()
                    ->live()
                    ->visible(fn($get) => $get('borrower_type') === 'existing')
                    ->required(fn($get) => $get('borrower_type') === 'existing'),

                TextInput::make('borrower_name')
                    ->label('Name')
                    ->visible(fn($get) => $get('borrower_type') === 'new')
                    ->required(fn($get) => $get('borrower_type') === 'new'),

                TextInput::make('borrower_phone')
                    ->label('Phone Number')
                    ->visible(fn($get) => $get('borrower_type') === 'new')
                    ->required(fn($get) => $get('borrower_type') === 'new'),
                TextInput::make('borrower_email')
                    ->email()
                    ->required(fn($get) => $get('borrower_type') === 'new')
                    ->visible(fn($get) => $get('borrower_type') === 'new')
                    ->unique(User::class, 'email')
                    ->autocomplete('off'),
                TextInput::make('borrower_password')
                    ->label('Password')
                    ->password()
                    ->required(fn($get) => $get('borrower_type') === 'new')
                    ->visible(fn($get) => $get('borrower_type') === 'new')
                    ->confirmed()
                    ->autocomplete('new-password'),
                TextInput::make('borrower_password_confirmation')
                    ->label('Konfirmasi Password')
                    ->password()
                    ->required(fn($get) => $get('borrower_type') === 'new')
                    ->visible(fn($get) => $get('borrower_type') === 'new')
                    ->autocomplete('new-password'),
                Textarea::make('borrower_address')
                    ->label('Address')
                    ->visible(fn($get) => $get('borrower_type') === 'new')
                    ->required(fn($get) => $get('borrower_type') === 'new'),

                Select::make('book_id')
                    ->label('Book')
                    ->options(function ($get) {

                        $borrowerId = $get('borrower_id');

                        return Book::all()
                            ->mapWithKeys(function ($book) use ($borrowerId) {

                                $isAlreadyBorrowed = false;

                                if ($borrowerId) {
                                    $isAlreadyBorrowed = Transaction::query()
                                        ->where('borrower_id', $borrowerId)
                                        ->where('book_id', $book->id)
                                        ->where('status', 'borrowed')
                                        ->exists();
                                }

                                if ($book->stock <= 0) {
                                    $status = 'Unavailable';
                                } elseif ($isAlreadyBorrowed) {
                                    $status = 'Already Borrowed';
                                } else {
                                    $status = 'Available';
                                }

                                return [
                                    $book->id =>
                                    "{$book->title} | Stock: {$book->stock} | {$status}",
                                ];
                            });
                    })
                    ->disableOptionWhen(function ($value, $get) {

                        $book = Book::find($value);

                        if (! $book) {
                            return true;
                        }

                        if ($book->stock <= 0) {
                            return true;
                        }

                        $borrowerId = $get('borrower_id');

                        if (! $borrowerId) {
                            return false;
                        }

                        return Transaction::query()
                            ->where('borrower_id', $borrowerId)
                            ->where('book_id', $value)
                            ->where('status', 'borrowed')
                            ->exists();
                    })
                    ->searchable()
                    ->required(),
            ]);
    }
}
