<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PoetResource\Pages;
use App\Filament\Resources\PoetResource\RelationManagers;
use App\Models\Poet;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PoetResource extends Resource
{
    protected static ?string $model = Poet::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('full_name')
                    ->maxLength(255),
                Forms\Components\TextInput::make('nom_de_plume')
                    ->maxLength(255),
                Forms\Components\TextInput::make('aka')
                    ->maxLength(255),
                Forms\Components\TextInput::make('era')
                    ->maxLength(255),
                Forms\Components\TextInput::make('nationality')
                    ->maxLength(255),
                Forms\Components\TextInput::make('birth_year')
                    ->maxLength(255),
                Forms\Components\TextInput::make('death_year')
                    ->maxLength(255),
                Forms\Components\TextInput::make('birth_place')
                    ->maxLength(255),
                Forms\Components\TextInput::make('death_place')
                    ->maxLength(255),
                Forms\Components\Textarea::make('biography')
                    ->maxLength(65535)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('full_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nom_de_plume')
                    ->searchable(),
                Tables\Columns\TextColumn::make('aka')
                    ->searchable(),
                Tables\Columns\TextColumn::make('era')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nationality')
                    ->searchable(),
                Tables\Columns\TextColumn::make('birth_year')
                    ->searchable(),
                Tables\Columns\TextColumn::make('death_year')
                    ->searchable(),
                Tables\Columns\TextColumn::make('birth_place')
                    ->searchable(),
                Tables\Columns\TextColumn::make('death_place')
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
            'index' => Pages\ManagePoets::route('/'),
        ];
    }
}
