<?php

namespace App\Filament\Resources\InvoiceResource\RelationManagers;

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
                    ->step(0.01)
                    ->reactive()
                    ->afterStateUpdated(fn ($state, callable $set, $get) => $this->calculateTotals($state, $set, $get)),
                Forms\Components\TextInput::make('unit_price')
                    ->required()
                    ->numeric()

                    ->minValue(0)
                    ->step(0.01)
                    ->reactive()
                    ->afterStateUpdated(fn ($state, callable $set, $get) => $this->calculateTotals($state, $set, $get)),
                Forms\Components\TextInput::make('tax_rate')
                    ->numeric()
                    ->suffix('%')
                    ->default(0)
                    ->minValue(0)
                    ->step(0.01)
                    ->reactive()
                    ->afterStateUpdated(fn ($state, callable $set, $get) => $this->calculateTotals($state, $set, $get)),
                Forms\Components\TextInput::make('tax_amount')
                    ->numeric()

                    ->disabled()
                    ->dehydrated(),
                Forms\Components\TextInput::make('discount_rate')
                    ->numeric()
                    ->suffix('%')
                    ->default(0)
                    ->minValue(0)
                    ->step(0.01)
                    ->reactive()
                    ->afterStateUpdated(fn ($state, callable $set, $get) => $this->calculateTotals($state, $set, $get)),
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
                    ->limit(30),
                Tables\Columns\TextColumn::make('quantity')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('unit_price')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tax_rate')
                    ->numeric()
                    ->suffix('%')
                    ->sortable(),
                Tables\Columns\TextColumn::make('discount_rate')
                    ->numeric()
                    ->suffix('%')
                    ->sortable(),
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
                        // Update the parent invoice totals
                        $this->updateInvoiceTotals();
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->after(function ($record, $data) {
                        // Update the parent invoice totals
                        $this->updateInvoiceTotals();
                    }),
                Tables\Actions\DeleteAction::make()
                    ->after(function ($record, $data) {
                        // Update the parent invoice totals
                        $this->updateInvoiceTotals();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->after(function () {
                            // Update the parent invoice totals
                            $this->updateInvoiceTotals();
                        }),
                ]),
            ]);
    }

    private function calculateTotals($state, callable $set, $get): void
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

    private function updateInvoiceTotals(): void
    {
        $invoice = $this->getOwnerRecord();
        $items = $invoice->items;

        $subtotal = $items->sum('subtotal');
        $tax = $items->sum('tax_amount');
        $discount = $items->sum('discount_amount');
        $total = $subtotal + $tax - $discount;
        $amountDue = $total - $invoice->amount_paid;

        $invoice->update([
            'subtotal' => $subtotal,
            'tax' => $tax,
            'discount' => $discount,
            'total' => $total,
            'amount_due' => $amountDue,
        ]);
    }
}
