<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

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
                    ->required()
                    ->default(1)
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set, $get) {
                        $quantity = floatval($state);
                        $unitPrice = floatval($get('unit_price'));
                        $taxRate = floatval($get('tax_rate'));
                        $discountRate = floatval($get('discount_rate'));
                        
                        $subtotal = $quantity * $unitPrice;
                        $taxAmount = $subtotal * ($taxRate / 100);
                        $discountAmount = $subtotal * ($discountRate / 100);
                        $total = $subtotal + $taxAmount - $discountAmount;
                        
                        $set('subtotal', $subtotal);
                        $set('tax_amount', $taxAmount);
                        $set('discount_amount', $discountAmount);
                        $set('total', $total);
                    }),
                Forms\Components\TextInput::make('unit')
                    ->maxLength(50),
                Forms\Components\TextInput::make('unit_price')
                    ->numeric()
                    ->required()
                    ->default(0)
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set, $get) {
                        $quantity = floatval($get('quantity'));
                        $unitPrice = floatval($state);
                        $taxRate = floatval($get('tax_rate'));
                        $discountRate = floatval($get('discount_rate'));
                        
                        $subtotal = $quantity * $unitPrice;
                        $taxAmount = $subtotal * ($taxRate / 100);
                        $discountAmount = $subtotal * ($discountRate / 100);
                        $total = $subtotal + $taxAmount - $discountAmount;
                        
                        $set('subtotal', $subtotal);
                        $set('tax_amount', $taxAmount);
                        $set('discount_amount', $discountAmount);
                        $set('total', $total);
                    }),
                Forms\Components\TextInput::make('tax_rate')
                    ->numeric()
                    ->default(0)
                    ->suffix('%')
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set, $get) {
                        $subtotal = floatval($get('subtotal'));
                        $taxRate = floatval($state);
                        
                        $taxAmount = $subtotal * ($taxRate / 100);
                        $discountAmount = floatval($get('discount_amount'));
                        $total = $subtotal + $taxAmount - $discountAmount;
                        
                        $set('tax_amount', $taxAmount);
                        $set('total', $total);
                    }),
                Forms\Components\TextInput::make('tax_amount')
                    ->numeric()
                    ->disabled()
                    ->dehydrated(),
                Forms\Components\TextInput::make('discount_rate')
                    ->numeric()
                    ->default(0)
                    ->suffix('%')
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set, $get) {
                        $subtotal = floatval($get('subtotal'));
                        $discountRate = floatval($state);
                        
                        $discountAmount = $subtotal * ($discountRate / 100);
                        $taxAmount = floatval($get('tax_amount'));
                        $total = $subtotal + $taxAmount - $discountAmount;
                        
                        $set('discount_amount', $discountAmount);
                        $set('total', $total);
                    }),
                Forms\Components\TextInput::make('discount_amount')
                    ->numeric()
                    ->disabled()
                    ->dehydrated(),
                Forms\Components\TextInput::make('subtotal')
                    ->numeric()
                    ->disabled()
                    ->dehydrated(),
                Forms\Components\TextInput::make('total')
                    ->numeric()
                    ->disabled()
                    ->dehydrated(),
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
                    ->limit(30)
                    ->toggleable(),
                Tables\Columns\TextColumn::make('quantity')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('unit')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('unit_price')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tax_rate')
                    ->numeric()
                    ->suffix('%')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('discount_rate')
                    ->numeric()
                    ->suffix('%')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('subtotal')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->after(function ($record, $data) {
                        // Update the order totals after creating an item
                        $order = $this->getOwnerRecord();
                        $items = $order->items;
                        
                        $subtotal = $items->sum('subtotal');
                        $tax = $items->sum('tax_amount');
                        $discount = $items->sum('discount_amount');
                        $total = $subtotal + $tax - $discount;
                        
                        $order->update([
                            'subtotal' => $subtotal,
                            'tax' => $tax,
                            'discount' => $discount,
                            'total' => $total,
                        ]);
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->after(function ($record, $data) {
                        // Update the order totals after editing an item
                        $order = $this->getOwnerRecord();
                        $items = $order->items;
                        
                        $subtotal = $items->sum('subtotal');
                        $tax = $items->sum('tax_amount');
                        $discount = $items->sum('discount_amount');
                        $total = $subtotal + $tax - $discount;
                        
                        $order->update([
                            'subtotal' => $subtotal,
                            'tax' => $tax,
                            'discount' => $discount,
                            'total' => $total,
                        ]);
                    }),
                Tables\Actions\DeleteAction::make()
                    ->after(function ($record, $data) {
                        // Update the order totals after deleting an item
                        $order = $this->getOwnerRecord();
                        $items = $order->items;
                        
                        $subtotal = $items->sum('subtotal');
                        $tax = $items->sum('tax_amount');
                        $discount = $items->sum('discount_amount');
                        $total = $subtotal + $tax - $discount;
                        
                        $order->update([
                            'subtotal' => $subtotal,
                            'tax' => $tax,
                            'discount' => $discount,
                            'total' => $total,
                        ]);
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->after(function () {
                            // Update the order totals after bulk deleting items
                            $order = $this->getOwnerRecord();
                            $items = $order->items;
                            
                            $subtotal = $items->sum('subtotal');
                            $tax = $items->sum('tax_amount');
                            $discount = $items->sum('discount_amount');
                            $total = $subtotal + $tax - $discount;
                            
                            $order->update([
                                'subtotal' => $subtotal,
                                'tax' => $tax,
                                'discount' => $discount,
                                'total' => $total,
                            ]);
                        }),
                ]),
            ]);
    }
}
