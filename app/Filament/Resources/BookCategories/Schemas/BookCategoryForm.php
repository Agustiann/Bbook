<?php

namespace App\Filament\Resources\BookCategories\Schemas;

use App\Models\BookCategory;
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

                TextInput::make('min_stock')
                    ->default(function () {
                        $lastCategory = BookCategory::query()
                            ->whereNotNull('max_stock')
                            ->orderByDesc('max_stock')
                            ->first();

                        return $lastCategory
                            ? $lastCategory->max_stock + 1
                            : 1;
                    })
                    ->disabled()
                    ->dehydrated(),

                TextInput::make('max_stock')
                    ->required()
                    ->numeric()
                    ->minValue(1)
                    ->rules([
                        function () {
                            $lastCategory = BookCategory::query()
                                ->whereNotNull('max_stock')
                                ->orderByDesc('max_stock')
                                ->first();

                            $minStock = $lastCategory
                                ? $lastCategory->max_stock + 1
                                : 1;

                            return "gte:$minStock";
                        },
                    ])
            ]);
    }
}