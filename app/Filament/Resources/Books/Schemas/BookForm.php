<?php

namespace App\Filament\Resources\Books\Schemas;

use App\Models\BookCategory;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class BookForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Hidden::make('category_id'),

                TextInput::make('category_name')
                    ->label('Kategori Buku')
                    ->disabled()
                    ->dehydrated(false),

                TextInput::make('title')
                    ->label('Judul Buku')
                    ->required(),

                TextInput::make('author')
                    ->label('Penulis')
                    ->required(),

                TextInput::make('publisher')
                    ->label('Penerbit')
                    ->required(),

                TextInput::make('publication_year')
                    ->label('Tahun Terbit')
                    ->numeric()
                    ->required(),

                TextInput::make('stock')
                    ->label('Stok')
                    ->numeric()
                    ->live()
                    ->afterStateUpdated(function ($state, callable $set) {

                        if ((int) $state >= 50) {
                            $category = BookCategory::where('category_name', 'Reguler')->first();
                        } elseif ((int) $state >= 21) {
                            $category = BookCategory::where('category_name', 'Premium')->first();
                        } else {
                            $category = BookCategory::where('category_name', 'Langka')->first();
                        }

                        $set('category_id', $category?->id);
                        $set('category_name', $category?->category_name);
                    })
                    ->required(),
            ]);
    }
}