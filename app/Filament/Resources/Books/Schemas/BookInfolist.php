<?php

namespace App\Filament\Resources\Books\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class BookInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Buku')
                    ->schema([
                        TextEntry::make('title')
                            ->label('Judul Buku'),

                        TextEntry::make('category.category_name')
                            ->label('Kategori'),

                        TextEntry::make('author')
                            ->label('Penulis'),

                        TextEntry::make('publisher')
                            ->label('Penerbit'),

                        TextEntry::make('publication_year')
                            ->label('Tahun Terbit'),

                        TextEntry::make('stock')
                            ->label('Stok'),

                        TextEntry::make('creator.name')
                            ->label('Created By'),

                        TextEntry::make('created_at')
                            ->label('Created At')
                            ->dateTime('d M Y H:i'),
                    ]),

                Section::make('Cover Buku')
                    ->schema([
                        ImageEntry::make('photo')
                            ->hiddenLabel()
                            ->disk('public')
                            ->height(250),

                        TextEntry::make('info')
                            ->hiddenLabel()
                            ->default('Gambar buku belum diupload.')
                            ->visible(fn($record) => blank($record->photo)),
                    ]),
            ]);
    }
}
