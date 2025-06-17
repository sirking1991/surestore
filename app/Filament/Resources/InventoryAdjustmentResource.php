<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InventoryAdjustmentResource\Pages;
use App\Filament\Resources\InventoryAdjustmentResource\RelationManagers;
use App\Models\InventoryAdjustment;
use App\Models\Product;
use App\Models\StorageLocation;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class InventoryAdjustmentResource extends Resource
{
    protected static ?string $model = InventoryAdjustment::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-path';
    
    protected static ?string $navigationGroup = 'Inventories';
    
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Header Section
                Forms\Components\Section::make('Adjustment Information')
                    ->schema([
                        Forms\Components\TextInput::make('reference_number')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        Forms\Components\DatePicker::make('adjustment_date')
                            ->required()
                            ->default(now()),
                        Forms\Components\Select::make('storage_location_id')
                            ->label('Storage Location')
                            ->options(StorageLocation::query()->pluck('name', 'id'))
                            ->required()
                            ->searchable(),
                        Forms\Components\Select::make('adjustment_type')
                            ->options([
                                'addition' => 'Addition',
                                'subtraction' => 'Subtraction',
                                'transfer' => 'Transfer',
                            ])
                            ->required()
                            ->default('addition'),
                        Forms\Components\Select::make('status')
                            ->options([
                                'draft' => 'Draft',
                                'approved' => 'Approved',
                                'cancelled' => 'Cancelled',
                            ])
                            ->default('draft')
                            ->required(),
                    ])
                    ->columns(2),
                
                // Notes Section
                Forms\Components\Section::make('Notes')
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->columnSpanFull(),
                    ]),
                
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('reference_number')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('adjustment_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('storageLocation.name')
                    ->label('Storage Location')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('adjustment_type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'addition' => 'success',
                        'subtraction' => 'danger',
                        'transfer' => 'info',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'approved' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('createdBy.name')
                    ->label('Created By')
                    ->toggleable(),
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
                Tables\Filters\TrashedFilter::make(),
                Tables\Filters\SelectFilter::make('adjustment_type')
                    ->options([
                        'addition' => 'Addition',
                        'subtraction' => 'Subtraction',
                        'transfer' => 'Transfer',
                    ]),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'approved' => 'Approved',
                        'cancelled' => 'Cancelled',
                    ]),
                Tables\Filters\SelectFilter::make('storage_location_id')
                    ->label('Storage Location')
                    ->relationship('storageLocation', 'name'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (InventoryAdjustment $record): bool => $record->status === 'draft')
                    ->action(function (InventoryAdjustment $record): void {
                        $record->status = 'approved';
                        $record->approved_by = auth()->id();
                        $record->approved_at = now();
                        $record->save();
                    })
                    ->after(fn () => Tables\Actions\Action::make('success')->success())
                    ->successNotificationTitle('Inventory adjustment approved successfully'),
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
            RelationManagers\ItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInventoryAdjustments::route('/'),
            'create' => Pages\CreateInventoryAdjustment::route('/create'),
            'edit' => Pages\EditInventoryAdjustment::route('/{record}/edit'),
        ];
    }
}
