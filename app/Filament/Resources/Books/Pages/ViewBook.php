<?php

namespace App\Filament\Resources\Books\Pages;

use App\Filament\Resources\Books\BookResource;
use App\Filament\Resources\Books\Schemas\BookInfolist;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Schema;

class ViewBook extends ViewRecord
{
    protected static string $resource = BookResource::class;
    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
    public function infolist(Schema $schema): Schema
    {
        return BookInfolist::configure($schema);
    }
}
