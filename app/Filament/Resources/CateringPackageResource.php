<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CateringPackageResource\Pages;
use App\Filament\Resources\CateringPackageResource\RelationManagers\BonusesRelationManager;
use App\Filament\Resources\CateringPackageResource\RelationManagers\TiersRelationManager;
use App\Models\CateringPackage;
use Filament\Forms;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CateringPackageResource extends Resource
{
    protected static ?string $model = CateringPackage::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Fieldset::make('Package Details')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\FileUpload::make('thumbnail')
                            ->required()
                            ->image(),
                        Forms\Components\Repeater::make('photos')
                            ->relationship('photos')
                            ->schema([
                                Forms\Components\FileUpload::make('photo')
                                    ->required(),
                            ]),
                    ]),
                Fieldset::make('Additional')
                    ->schema([
                        Forms\Components\Textarea::make('about')
                            ->required(),
                        Forms\Components\Select::make('is_popular')
                        ->options([
                            true => 'Pupular',
                            false => 'Not Popular',
                        ])
                        ->required(),

                        Forms\Components\Select::make('city_id')
                        ->relationship('city', 'name')
                        ->searchable()
                        ->preload()
                        ->required(),

                        Forms\Components\Select::make('kitchen_id')
                        ->relationship('kitchen', 'name')
                        ->searchable()
                        ->preload()
                        ->required(),

                        Forms\Components\Select::make('category_id')
                        ->relationship('category', 'name')
                        ->searchable()
                        ->preload()
                        ->required(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('thumbnail'),
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('kitchen.name')->label('Kitchen')->searchable(),
                Tables\Columns\IconColumn::make('is_popular')->boolean()->label('Popular')
                ->boolean()
                ->trueColor('success')
                ->falseColor('danger')
                ->trueIcon('heroicon-o-check-circle')
                ->falseIcon('heroicon-o-x-circle')
                ->label('Popular'),
            ])
            ->filters([
                SelectFilter::make('city_id')
                    ->label('City')
                    ->relationship('city', 'name'),
                    SelectFilter::make('kitchen_id')
                    ->label('Kitchen')
                    ->relationship('kitchen', 'name'),
                    SelectFilter::make('category_id')
                    ->label('Category')
                    ->relationship('category', 'name'),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            BonusesRelationManager::class,
            TiersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCateringPackages::route('/'),
            'create' => Pages\CreateCateringPackage::route('/create'),
            'edit' => Pages\EditCateringPackage::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
