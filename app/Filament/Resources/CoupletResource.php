<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CoupletResource\Pages;
use App\Filament\Resources\CoupletResource\RelationManagers;
use App\Models\Couplet;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CoupletResource extends Resource
{
    protected static ?string $model = Couplet::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('poem_id')
                    ->numeric(),
                Forms\Components\TextInput::make('number_of_couplet')
                    ->numeric(),
                Forms\Components\TextInput::make('first_line')
                    ->maxLength(255),
                Forms\Components\TextInput::make('second_line')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('poem_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('number_of_couplet')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('first_line')
                    ->searchable(),
                Tables\Columns\TextColumn::make('second_line')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageCouplets::route('/'),
        ];
    }
}
