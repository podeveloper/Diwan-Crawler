<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PoemResource\Pages;
use App\Filament\Resources\PoemResource\RelationManagers;
use App\Models\Poem;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PoemResource extends Resource
{
    protected static ?string $model = Poem::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('poet_id')
                    ->relationship('poet', 'full_name')
                    ->searchable()
                    ->preload()
                    ->nullable()
                    ->exists('poets','id'),
                Forms\Components\TextInput::make('number_of_poem')
                    ->numeric(),
                Forms\Components\TextInput::make('title')
                    ->maxLength(255),
                Forms\Components\TextInput::make('type')
                    ->maxLength(255),
                Forms\Components\TextInput::make('couplet_count')
                    ->maxLength(255),
                Forms\Components\TextInput::make('meter')
                    ->maxLength(255),
                Forms\Components\TextInput::make('url')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('number_of_poem')
                    ->numeric()
                    ->toggleable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->toggleable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->toggleable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('poet.full_name')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('couplet_count')
                    ->toggleable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('meter')
                    ->toggleable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('url')
                    ->toggleable()
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
            'index' => Pages\ManagePoems::route('/'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return Poem::count();
    }
}
