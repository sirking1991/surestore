<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PurchaseDeliveryResource\Pages;
use App\Filament\Resources\PurchaseDeliveryResource\RelationManagers;
use App\Models\PurchaseDelivery;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PurchaseDeliveryResource extends Resource
{
    protected static ?string $model = PurchaseDelivery::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';

    protected static ?string $navigationGroup = 'Purchases';
    
    protected static ?int $navigationSort = 20;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Delivery Information')
                    ->schema([
                        Forms\Components\TextInput::make('code')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->disabled(fn ($record) => $record !== null)
                            ->default(function () {
                                // Generate a unique delivery number with format GRN-YYYYMMDD-XXXX
                                $date = now()->format('Ymd');
                                $latestDelivery = PurchaseDelivery::whereDate('created_at', today())
                                    ->latest()
                                    ->first();
                                
                                $sequence = $latestDelivery 
                                    ? (int) substr($latestDelivery->code, -4) + 1 
                                    : 1;
                                
                                return 'GRN-' . $date . '-' . str_pad($sequence, 4, '0', STR_PAD_LEFT);
                            }),
                        Forms\Components\Select::make('supplier_id')
                            ->relationship('supplier', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('purchase_order_id')
                            ->relationship('purchaseOrder', 'code')
                            ->searchable()
                            ->preload(),
                        Forms\Components\DatePicker::make('delivery_date')
                            ->required()
                            ->default(now()),
                        Forms\Components\DatePicker::make('expected_delivery_date'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Shipping Details')
                    ->schema([
                        Forms\Components\TextInput::make('tracking_number')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('carrier')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('shipping_method')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('shipping_cost')
                            ->numeric()
,
                        Forms\Components\TextInput::make('other_charges')
                            ->numeric()
,
                    ])
                    ->columns(2),
                    
                Forms\Components\Section::make('Measurement Information')
                    ->schema([
                        Forms\Components\TextInput::make('total_weight')
                            ->numeric(),
                        Forms\Components\TextInput::make('weight_unit')
                            ->maxLength(255)
                            ->default('kg'),
                        Forms\Components\TextInput::make('total_volume')
                            ->numeric(),
                        Forms\Components\TextInput::make('volume_unit')
                            ->maxLength(255)
                            ->default('m³'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Additional Information')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'in-transit' => 'In Transit',
                                'delivered' => 'Delivered',
                                'cancelled' => 'Cancelled',
                            ])
                            ->default('pending')
                            ->required(),
                        Forms\Components\Textarea::make('notes')
                            ->maxLength(65535)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('supplier.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('purchaseOrder.code')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('delivery_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('expected_delivery_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tracking_number')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('carrier')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'pending',
                        'primary' => 'in-transit',
                        'success' => 'delivered',
                        'danger' => 'cancelled',
                    ]),
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
                Tables\Filters\SelectFilter::make('supplier_id')
                    ->relationship('supplier', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Supplier'),
                Tables\Filters\SelectFilter::make('purchase_order_id')
                    ->relationship('purchaseOrder', 'code')
                    ->searchable()
                    ->preload()
                    ->label('Purchase Order'),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'in-transit' => 'In Transit',
                        'delivered' => 'Delivered',
                        'cancelled' => 'Cancelled',
                    ]),
                Tables\Filters\Filter::make('delivery_date')
                    ->form([
                        Forms\Components\DatePicker::make('delivered_from'),
                        Forms\Components\DatePicker::make('delivered_until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['delivered_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('delivery_date', '>=', $date),
                            )
                            ->when(
                                $data['delivered_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('delivery_date', '<=', $date),
                            );
                    }),
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

    public static function getRelations(): array
    {
        return [
            RelationManagers\ItemsRelationManager::class,
            RelationManagers\InvoicesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPurchaseDeliveries::route('/'),
            'create' => Pages\CreatePurchaseDelivery::route('/create'),
            'view' => Pages\ViewPurchaseDelivery::route('/{record}'),
            'edit' => Pages\EditPurchaseDelivery::route('/{record}/edit'),
        ];
    }
}
