<?php

namespace App\Filament\Resources\CoupletResource\Pages;

use App\Filament\Resources\CoupletResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageCouplets extends ManageRecords
{
    protected static string $resource = CoupletResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
