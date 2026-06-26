<?php

namespace App\Filament\Resources\Books\Schemas;

use App\Models\BookCategory;
use Filament\Forms\Components\FileUpload;
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
                    ->afterStateHydrated(function ($state, callable $set, $record) {
                        $category = BookCategory::getCategoryByStock((int) $state);

                        if ($category) {
                            $set('category_id', $category->id);
                            $set('category_name', $category->category_name);
                        } elseif ($record) {
                            $set('category_id', $record->category_id);
                            $set('category_name', $record->category?->category_name);
                        }
                    })
                    ->afterStateUpdated(function ($state, callable $set, $record) {
                        $category = BookCategory::getCategoryByStock((int) $state);

                        if ($category) {
                            $set('category_id', $category->id);
                            $set('category_name', $category->category_name);
                        } elseif ($record) {
                            $set('category_id', $record->category_id);
                            $set('category_name', $record->category?->category_name);
                        }
                    })
                    ->required(),
                FileUpload::make('photo')
                    ->label('Foto Buku')
                    ->image()
                    ->imageEditor()
                    ->directory('books')
                    ->disk('private')
                    ->visibility('private')
                    ->maxSize(2048)
                    ->columnSpanFull(),
            ]);
    }
}
