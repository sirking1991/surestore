<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StorageLocationResource\Pages;
use App\Filament\Resources\StorageLocationResource\RelationManagers;
use App\Models\StorageLocation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StorageLocationResource extends Resource
{
    protected static ?string $model = StorageLocation::class;

    protected static ?string $navigationIcon = 'heroicon-o-map-pin';

    protected static ?string $navigationGroup = 'Master Data';
    
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Basic Information')
                    ->schema([
                        Forms\Components\Select::make('storage_id')
                            ->relationship('storage', 'name')
                            ->required()
                            ->searchable(),
                        Forms\Components\TextInput::make('code')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Toggle::make('is_active')
                            ->required()
                            ->default(true),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Location Details')
                    ->schema([
                        Forms\Components\Textarea::make('description')
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('capacity')
                            ->numeric()
                            ->step(0.01),
                        Forms\Components\Select::make('capacity_unit')
                            ->required()
                            ->options([
                                'sqm' => 'Square Meter',
                                'sqft' => 'Square Feet',
                                'cbm' => 'Cubic Meter',
                                'cbft' => 'Cubic Feet',
                            ])
                            ->default('sqm'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Location Coordinates')
                    ->schema([
                        Forms\Components\TextInput::make('zone')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('aisle')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('rack')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('shelf')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('bin')
                            ->maxLength(255),
                    ])
                    ->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('storage.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('code')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('full_code')
                    ->label('Full Location Code')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('location_path')
                    ->label('Location Path')
                    ->searchable(),
                Tables\Columns\TextColumn::make('capacity')
                    ->numeric(2)
                    ->suffix(fn (StorageLocation $record): string => ' ' . $record->capacity_unit)
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
                Tables\Filters\SelectFilter::make('storage_id')
                    ->relationship('storage', 'name')
                    ->label('Storage')
                    ->searchable(),
                Tables\Filters\SelectFilter::make('is_active')
                    ->options([
                        '1' => 'Active',
                        '0' => 'Inactive',
                    ])
                    ->label('Status'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStorageLocations::route('/'),
            'create' => Pages\CreateStorageLocation::route('/create'),
            'view' => Pages\ViewStorageLocation::route('/{record}'),
            'edit' => Pages\EditStorageLocation::route('/{record}/edit'),
        ];
    }
}
