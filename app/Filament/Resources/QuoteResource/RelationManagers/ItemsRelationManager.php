<?php

namespace App\Filament\Resources\QuoteResource\RelationManagers;

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
                                $set('unit_price', $product->selling_price);
                                $set('unit', $product->unit);
                                $set('description', $product->description);
                            }
                        }
                    }),
                Forms\Components\TextInput::make('description')
                    ->maxLength(255),
                Forms\Components\TextInput::make('quantity')
                    ->numeric()
                    ->default(1)
                    ->required()
                    ->minValue(0.01)
                    ->step(0.01)
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                        $this->calculateItemTotals($state, $set, $get);
                    }),
                Forms\Components\TextInput::make('unit')
                    ->maxLength(255)
                    ->default('pcs'),
                Forms\Components\TextInput::make('unit_price')
                    ->numeric()
                    ->required()
                    ->minValue(0)
                    ->step(0.01)
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                        $this->calculateItemTotals($state, $set, $get);
                    }),
                Forms\Components\TextInput::make('tax_rate')
                    ->numeric()
                    ->default(0)
                    ->suffix('%')
                    ->minValue(0)
                    ->maxValue(100)
                    ->step(0.01)
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                        $this->calculateItemTotals($state, $set, $get);
                    }),
                Forms\Components\TextInput::make('discount_rate')
                    ->numeric()
                    ->default(0)
                    ->suffix('%')
                    ->minValue(0)
                    ->maxValue(100)
                    ->step(0.01)
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                        $this->calculateItemTotals($state, $set, $get);
                    }),
                Forms\Components\TextInput::make('subtotal')
                    ->numeric()
                    ->disabled()
                    ->dehydrated(),
                Forms\Components\TextInput::make('tax_amount')
                    ->numeric()
                    ->disabled()
                    ->dehydrated(),
                Forms\Components\TextInput::make('discount_amount')
                    ->numeric()
                    ->disabled()
                    ->dehydrated(),
                Forms\Components\TextInput::make('total')
                    ->numeric()
                    ->disabled()
                    ->dehydrated(),
                Forms\Components\TextInput::make('notes')
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('product.name')
                    ->label('Product')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('description')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('quantity')
                    ->numeric(2)
                    ->sortable(),
                Tables\Columns\TextColumn::make('unit')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('unit_price')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tax_rate')
                    ->numeric(2)
                    ->suffix('%')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('discount_rate')
                    ->numeric(2)
                    ->suffix('%')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('subtotal')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total')
                    ->numeric()
                    ->sortable(),
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
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->after(function () {
                        $this->updateQuoteTotals();
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->after(function () {
                        $this->updateQuoteTotals();
                    }),
                Tables\Actions\DeleteAction::make()
                    ->after(function () {
                        $this->updateQuoteTotals();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->after(function () {
                            $this->updateQuoteTotals();
                        }),
                ]),
            ]);
    }

    protected function calculateItemTotals($state, callable $set, callable $get): void
    {
        $quantity = (float) $get('quantity');
        $unitPrice = (float) $get('unit_price');
        $taxRate = (float) $get('tax_rate');
        $discountRate = (float) $get('discount_rate');
        
        $subtotal = $quantity * $unitPrice;
        $taxAmount = $subtotal * ($taxRate / 100);
        $discountAmount = $subtotal * ($discountRate / 100);
        $total = $subtotal + $taxAmount - $discountAmount;
        
        $set('subtotal', $subtotal);
        $set('tax_amount', $taxAmount);
        $set('discount_amount', $discountAmount);
        $set('total', $total);
    }
    
    protected function updateQuoteTotals(): void
    {
        $quote = $this->getOwnerRecord();
        $items = $quote->items;
        
        $subtotal = $items->sum('subtotal');
        $tax = $items->sum('tax_amount');
        $discount = $items->sum('discount_amount');
        $total = $subtotal + $tax - $discount;
        
        $quote->update([
            'subtotal' => $subtotal,
            'tax' => $tax,
            'discount' => $discount,
            'total' => $total,
        ]);
    }
}
