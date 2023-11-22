<?php

namespace App\Filament\Resources\PoetResource\Pages;

use App\Filament\Resources\PoetResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManagePoets extends ManageRecords
{
    protected static string $resource = PoetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
