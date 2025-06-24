<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use App\Models\ProductStorageMinQuantity;
use App\Models\StorageLocation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';

    protected static ?string $navigationGroup = 'Master Files';
    
    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Basic Information')
                    ->schema([
                        Forms\Components\TextInput::make('code')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('description')
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('sku')
                            ->label('SKU')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('barcode')
                            ->maxLength(255),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Classification')
                    ->schema([
                        Forms\Components\Select::make('supplier_id')
                            ->relationship('supplier', 'name')
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('code')
                                    ->required()
                                    ->unique()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                            ])
                            ->createOptionAction(function (Forms\Components\Actions\Action $action) {
                                return $action
                                    ->modalHeading('Create new supplier')
                                    ->modalSubmitActionLabel('Create supplier')
                                    ->modalWidth('lg');
                            }),
                        Forms\Components\Select::make('storage_location_id')
                            ->relationship('storageLocation', 'name')
                            ->searchable()
                            ->preload()
                            ->optionsLimit(100),
                        Forms\Components\TextInput::make('category')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('brand')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('unit')
                            ->maxLength(255)
                            ->required()
                            ->default('pcs'),
                        Forms\Components\Toggle::make('is_active')
                            ->required()
                            ->default(true),
                        Forms\Components\Toggle::make('is_service')
                            ->required()
                            ->default(false)
                            ->helperText('Is this a service instead of a physical product?'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Pricing')
                    ->schema([
                        Forms\Components\TextInput::make('purchase_price')
                            ->label('Purchase Price')
                            ->numeric()
                            ->step(0.01)
,
                        Forms\Components\TextInput::make('selling_price')
                            ->label('Selling Price')
                            ->numeric()
                            ->step(0.01)
,
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Inventory')
                    ->schema([
                        Forms\Components\TextInput::make('stock')
                            ->numeric()
                            ->default(0)
                            ->disabled()
                            ->helperText('Stock is updated automatically by transactions'),
                        Forms\Components\TextInput::make('min_stock')
                            ->numeric()
                            ->default(0)
                            ->helperText('Global minimum stock level'),
                        Forms\Components\Repeater::make('storageMinQuantities')
                            ->relationship()
                            ->schema([
                                Forms\Components\Select::make('storage_id')
                                    ->label('Storage')
                                    ->options(\App\Models\Storage::pluck('name', 'id'))
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->required()
                                    ->afterStateUpdated(fn ($set) => $set('storage_location_id', null)),
                                    
                                Forms\Components\Select::make('storage_location_id')
                                    ->label('Location')
                                    ->options(function (callable $get, $state, $record) {
                                        $storageId = $get('storage_id');
                                        if (!$storageId) {
                                            return [];
                                        }
                                        return StorageLocation::where('storage_id', $storageId)->pluck('name', 'id');
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->nullable()
                                    ->placeholder('Optional'),
                                    
                                Forms\Components\TextInput::make('min_quantity')
                                    ->label('Min Qty')
                                    ->numeric()
                                    ->default(0)
                                    ->required(),
                            ])
                            ->columns(3)
                            ->itemLabel(function (array $state, $record, $context) {
                                // For existing records with loaded relationships
                                if (isset($state['storage']) && is_array($state['storage'])) {
                                    $storageName = $state['storage']['name'] ?? 'Storage';
                                    $locationName = isset($state['storageLocation']['name']) ? ' - ' . $state['storageLocation']['name'] : '';
                                    return $storageName . $locationName;
                                }
                                
                                // For new items or when relationships aren't loaded yet
                                $storageId = $state['storage_id'] ?? null;
                                $locationId = $state['storage_location_id'] ?? null;
                                
                                if (!$storageId) return 'Select a storage';
                                
                                // Try to get storage name from the options if not in state
                                $storage = $storageId ? \App\Models\Storage::find($storageId) : null;
                                $storageName = $storage ? $storage->name : 'Storage #' . $storageId;
                                
                                // If we have a location ID, try to get its name
                                $locationName = '';
                                if ($locationId) {
                                    $location = StorageLocation::find($locationId);
                                    $locationName = $location ? ' - ' . $location->name : ' - Location #' . $locationId;
                                }
                                
                                return $storageName . $locationName;
                            })
                            ->addActionLabel('Add Location-Specific Minimum')
                            ->reorderable()
                            ->collapsible()
                            ->collapsed()
                            ->defaultItems(0),
                    ]),

                Forms\Components\Section::make('Media')
                    ->schema([
                        Forms\Components\FileUpload::make('image')
                            ->image()
                            ->directory('products')
                            ->visibility('public')
                            ->maxSize(1024)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->defaultImageUrl(fn () => asset('images/default-product.png'))
                    ->circular(),
                Tables\Columns\TextColumn::make('code')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('supplier.name')
                    ->label('Supplier')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('storageLocation.name')
                    ->label('Location')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('category')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('brand')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('unit')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('purchase_price')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('selling_price')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('stock')
                    ->numeric(0)
                    ->sortable()
                    ->color(function (Product $record) {
                        // Check if stock is below global min_stock
                        if ($record->stock <= $record->min_stock) {
                            return 'danger';
                        }
                        
                        // Check if stock is below any location-specific minimum
                        $lowStockLocation = $record->storageMinQuantities
                            ->first(fn ($item) => $record->stock <= $item->min_quantity);
                            
                        return $lowStockLocation ? 'warning' : 'success';
                    })
                    ->description(function (Product $record) {
                        $lowStockLocations = $record->storageMinQuantities
                            ->filter(fn ($item) => $record->stock <= $item->min_quantity)
                            ->map(fn ($item) => "{$item->storageLocation->name} (≤{$item->min_quantity})")
                            ->implode(', ');
                            
                        if ($lowStockLocations) {
                            return "Low in: $lowStockLocations";
                        }
                        
                        return null;
                    })
                    ->tooltip(function (Product $record) {
                        $locations = $record->storageMinQuantities
                            ->map(fn ($item) => "{$item->storageLocation->name}: {$item->min_quantity}")
                            ->join("\n");
                            
                        return "Minimum quantities by location:\n$locations";
                    }),
                Tables\Columns\TextColumn::make('min_stock')
                    ->label('Global Min')
                    ->numeric(0)
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('sku')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('barcode')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_service')
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
                Tables\Columns\IconColumn::make('is_raw_material')
                    ->boolean(),
                Tables\Columns\IconColumn::make('can_be_produced')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('supplier_id')
                    ->relationship('supplier', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Supplier'),
                Tables\Filters\SelectFilter::make('storage_location_id')
                    ->relationship('storageLocation', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Location'),
                Tables\Filters\SelectFilter::make('category')
                    ->searchable(),
                Tables\Filters\SelectFilter::make('brand')
                    ->searchable(),
                Tables\Filters\SelectFilter::make('is_active')
                    ->options([
                        '1' => 'Active',
                        '0' => 'Inactive',
                    ])
                    ->label('Status'),
                Tables\Filters\SelectFilter::make('is_service')
                    ->options([
                        '1' => 'Service',
                        '0' => 'Product',
                    ])
                    ->label('Type'),
                Tables\Filters\Filter::make('low_stock')
                    ->query(fn (Builder $query): Builder => $query->whereColumn('stock', '<=', 'min_stock'))
                    ->label('Low Stock'),
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
            //
        ];
    }
    
    public static function getGlobalSearchResultUrl(Model $record): ?string
    {
        // Ensure the edit page is used for global search results
        return self::getUrl('edit', ['record' => $record]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'view' => Pages\ViewProduct::route('/{record}'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
