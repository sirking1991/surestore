<?php

namespace App\Filament\Resources\DeliveryResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

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
                                $set('description', $product->description);
                            }
                        }
                    }),
                Forms\Components\TextInput::make('description')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('quantity')
                    ->required()
                    ->numeric()
                    ->default(1)
                    ->minValue(0.01)
                    ->step(0.01),
                Forms\Components\Select::make('order_item_id')
                    ->relationship('orderItem', 'id')
                    ->searchable()
                    ->preload()
                    ->label('Order Item'),
                Forms\Components\Select::make('storage_id')
                    ->relationship('storage', 'name')
                    ->searchable()
                    ->preload(),
                Forms\Components\Select::make('storage_location_id')
                    ->relationship('storageLocation', 'name')
                    ->searchable()
                    ->preload(),
                Forms\Components\TextInput::make('weight')
                    ->numeric()
                    ->step(0.01)
                    ->suffix('kg'),
                Forms\Components\TextInput::make('volume')
                    ->numeric()
                    ->step(0.01)
                    ->suffix('m³'),
                Forms\Components\TextInput::make('quantity_received')
                    ->numeric()
                    ->step(0.01)
                    ->default(0),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('product.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('description')
                    ->searchable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('quantity')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('quantity_received')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('weight')
                    ->numeric()
                    ->suffix('kg')
                    ->sortable(),
                Tables\Columns\TextColumn::make('volume')
                    ->numeric()
                    ->suffix('m³')
                    ->sortable(),
                Tables\Columns\TextColumn::make('storage.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('storageLocation.name')
                    ->searchable()
                    ->sortable(),
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
