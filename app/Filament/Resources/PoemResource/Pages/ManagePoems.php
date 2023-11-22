<?php

namespace App\Filament\Resources\PoemResource\Pages;

use App\Filament\Resources\PoemResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManagePoems extends ManageRecords
{
    protected static string $resource = PoemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
