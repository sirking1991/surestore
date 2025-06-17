<?php

namespace App\Filament\Resources\ProductionResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductsRelationManager extends RelationManager
{
    protected static string $relationship = 'products';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('product_id')
                    ->relationship('product', 'name')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        if ($state) {
                            $product = \App\Models\Product::find($state);
                            if ($product) {
                                $set('unit_cost', $product->cost_price);
                            }
                        }
                    }),
                Forms\Components\TextInput::make('quantity')
                    ->required()
                    ->numeric()
                    ->minValue(0.01)
                    ->step(0.01)
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set, $get) {
                        $unitCost = $get('unit_cost') ?? 0;
                        $quantity = $state ?? 0;
                        $set('total_cost', round($unitCost * $quantity, 2));
                    }),
                Forms\Components\TextInput::make('unit_cost')
                    ->required()
                    ->numeric()
                    ->prefix('$')
                    ->minValue(0)
                    ->step(0.01)
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set, $get) {
                        $quantity = $get('quantity') ?? 0;
                        $unitCost = $state ?? 0;
                        $set('total_cost', round($unitCost * $quantity, 2));
                    }),
                Forms\Components\TextInput::make('total_cost')
                    ->required()
                    ->numeric()
                    ->prefix('$')
                    ->disabled(),
                Forms\Components\Select::make('storage_id')
                    ->relationship('storage', 'name')
                    ->searchable()
                    ->preload(),
                Forms\Components\Select::make('storage_location_id')
                    ->relationship('storageLocation', 'name')
                    ->searchable()
                    ->preload(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('product.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('quantity')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('unit_cost')
                    ->money('USD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_cost')
                    ->money('USD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('storage.name')
                    ->label('Storage')
                    ->sortable(),
                Tables\Columns\TextColumn::make('storageLocation.name')
                    ->label('Location')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
