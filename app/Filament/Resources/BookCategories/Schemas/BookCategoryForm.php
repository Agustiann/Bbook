<?php

namespace App\Filament\Resources\BookCategories\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class BookCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('category_name')
                    ->required(),
                TextInput::make('fine_amount')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('max_borrow_days')
                    ->required()
                    ->numeric(),
            ]);
    }
}
