<?php

namespace App\Filament\Resources\Books\RelationManagers;

use App\Filament\Resources\Books\BookResource;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class TransactionsRelationManager extends RelationManager
{
    protected static string $relationship = 'transactions';

    protected static ?string $relatedResource = BookResource::class;
    
    protected function getTableHeading(): string
    {
        return 'Log Peminjaman Buku';
    }
    
    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('borrower.name'),
                TextColumn::make('borrowed_at'),
                TextColumn::make('returned_at'),
                TextColumn::make('due_date'),
                TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'warning' => 'borrowed',
                        'info' => 'extended',
                        'success' => 'returned',
                        'danger' => 'late',
                    ]),
            ])->inverseRelationship('section')
            ->headerActions([]);
            
    }
}
