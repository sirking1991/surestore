<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QuoteResource\Pages;
use App\Filament\Resources\QuoteResource\RelationManagers;
use App\Models\Quote;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class QuoteResource extends Resource
{
    protected static ?string $model = Quote::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Sales';
    
    protected static ?int $navigationSort = 10;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Quote Information')
                    ->schema([
                        Forms\Components\TextInput::make('quote_number')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->disabled(fn ($record) => $record !== null)
                            ->default(function () {
                                // Generate a unique quote number with format QT-YYYYMMDD-XXXX
                                $date = now()->format('Ymd');
                                $latestQuote = Quote::whereDate('created_at', today())
                                    ->latest()
                                    ->first();
                                
                                $sequence = $latestQuote 
                                    ? (int) substr($latestQuote->quote_number, -4) + 1 
                                    : 1;
                                
                                return 'QT-' . $date . '-' . str_pad($sequence, 4, '0', STR_PAD_LEFT);
                            }),
                        Forms\Components\Select::make('customer_id')
                            ->relationship('customer', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\DatePicker::make('quote_date')
                            ->required()
                            ->default(now()),
                        Forms\Components\DatePicker::make('valid_until')
                            ->required()
                            ->default(now()->addDays(30)),
                    ])
                    ->columns(2),

                // Forms\Components\Section::make('Quote Items')
                //     ->schema([
                //         Forms\Components\Repeater::make('items')
                //             ->relationship()
                //             ->schema([
                //                 Forms\Components\Select::make('product_id')
                //                     ->relationship('product', 'name')
                //                     ->required()
                //                     ->searchable()
                //                     ->preload()
                //                     ->reactive()
                //                     ->afterStateUpdated(function ($state, callable $set) {
                //                         if ($state) {
                //                             $product = \App\Models\Product::find($state);
                //                             if ($product) {
                //                                 $set('unit_price', $product->selling_price);
                //                                 $set('unit', $product->unit);
                //                                 $set('description', $product->description);
                //                             }
                //                         }
                //                     }),
                //                 Forms\Components\TextInput::make('description')
                //                     ->maxLength(255),
                //                 Forms\Components\TextInput::make('quantity')
                //                     ->numeric()
                //                     ->default(1)
                //                     ->required()
                //                     ->minValue(0.01)
                //                     ->step(0.01)
                //                     ->reactive(),
                //                 Forms\Components\TextInput::make('unit')
                //                     ->maxLength(255)
                //                     ->default('pcs'),
                //                 Forms\Components\TextInput::make('unit_price')
                //                     ->numeric()
                //                     ->required()
                //                     ->minValue(0)
                //                     ->step(0.01)
                //                     ->reactive(),
                //                 Forms\Components\TextInput::make('tax_rate')
                //                     ->numeric()
                //                     ->default(0)
                //                     ->suffix('%')
                //                     ->minValue(0)
                //                     ->maxValue(100)
                //                     ->step(0.01)
                //                     ->reactive(),
                //                 Forms\Components\TextInput::make('discount_rate')
                //                     ->numeric()
                //                     ->default(0)
                //                     ->suffix('%')
                //                     ->minValue(0)
                //                     ->maxValue(100)
                //                     ->step(0.01)
                //                     ->reactive(),
                //                 Forms\Components\Placeholder::make('subtotal')
                //                     ->content(function (callable $get) {
                //                         $quantity = (float) $get('quantity');
                //                         $unitPrice = (float) $get('unit_price');
                //                         return number_format($quantity * $unitPrice, 2);
                //                     }),
                //                 Forms\Components\Placeholder::make('tax_amount')
                //                     ->content(function (callable $get) {
                //                         $quantity = (float) $get('quantity');
                //                         $unitPrice = (float) $get('unit_price');
                //                         $taxRate = (float) $get('tax_rate');
                //                         $subtotal = $quantity * $unitPrice;
                //                         return number_format($subtotal * ($taxRate / 100), 2);
                //                     }),
                //                 Forms\Components\Placeholder::make('discount_amount')
                //                     ->content(function (callable $get) {
                //                         $quantity = (float) $get('quantity');
                //                         $unitPrice = (float) $get('unit_price');
                //                         $discountRate = (float) $get('discount_rate');
                //                         $subtotal = $quantity * $unitPrice;
                //                         return number_format($subtotal * ($discountRate / 100), 2);
                //                     }),
                //                 Forms\Components\Placeholder::make('total')
                //                     ->content(function (callable $get) {
                //                         $quantity = (float) $get('quantity');
                //                         $unitPrice = (float) $get('unit_price');
                //                         $taxRate = (float) $get('tax_rate');
                //                         $discountRate = (float) $get('discount_rate');
                                        
                //                         $subtotal = $quantity * $unitPrice;
                //                         $taxAmount = $subtotal * ($taxRate / 100);
                //                         $discountAmount = $subtotal * ($discountRate / 100);
                                        
                //                         return number_format($subtotal + $taxAmount - $discountAmount, 2);
                //                     }),
                //                 Forms\Components\TextInput::make('notes')
                //                     ->maxLength(255),
                //             ])
                //             ->columns(2)
                //             ->defaultItems(1)
                //             ->reorderable()
                //             ->collapsible()
                //             ->cloneable()
                //             ->itemLabel(fn (array $state): ?string => $state['description'] ?? null),
                //     ]),

                Forms\Components\Section::make('Quote Summary')
                    ->schema([
                        Forms\Components\TextInput::make('subtotal')
                            ->numeric()
                            ->disabled()
                            ->dehydrated(),
                        Forms\Components\TextInput::make('tax')
                            ->numeric()
                            ->disabled()
                            ->dehydrated(),
                        Forms\Components\TextInput::make('discount')
                            ->numeric()
                            ->disabled()
                            ->dehydrated(),
                        Forms\Components\TextInput::make('total')
                            ->numeric()
                            ->disabled()
                            ->dehydrated(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Additional Information')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                'draft' => 'Draft',
                                'sent' => 'Sent',
                                'accepted' => 'Accepted',
                                'rejected' => 'Rejected',
                                'expired' => 'Expired',
                            ])
                            ->default('draft')
                            ->required(),
                        Forms\Components\Textarea::make('notes')
                            ->maxLength(65535)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('quote_number')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('customer.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('quote_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('valid_until')
                    ->date()
                    ->sortable()
                    ->color(fn (Quote $record): string => 
                        $record->valid_until < now() ? 'danger' : 'success'
                    ),
                Tables\Columns\TextColumn::make('total')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'draft',
                        'primary' => 'sent',
                        'success' => 'accepted',
                        'danger' => 'rejected',
                        'gray' => 'expired',
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
                Tables\Filters\SelectFilter::make('customer_id')
                    ->relationship('customer', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Customer'),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'sent' => 'Sent',
                        'accepted' => 'Accepted',
                        'rejected' => 'Rejected',
                        'expired' => 'Expired',
                    ]),
                Tables\Filters\Filter::make('quote_date')
                    ->form([
                        Forms\Components\DatePicker::make('created_from'),
                        Forms\Components\DatePicker::make('created_until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('quote_date', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('quote_date', '<=', $date),
                            );
                    }),
                Tables\Filters\Filter::make('valid_until')
                    ->form([
                        Forms\Components\DatePicker::make('valid_from'),
                        Forms\Components\DatePicker::make('valid_until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['valid_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('valid_until', '>=', $date),
                            )
                            ->when(
                                $data['valid_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('valid_until', '<=', $date),
                            );
                    }),
                Tables\Filters\Filter::make('expired')
                    ->query(fn (Builder $query): Builder => $query->whereDate('valid_until', '<', now()))
                    ->label('Expired Quotes'),
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
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListQuotes::route('/'),
            'create' => Pages\CreateQuote::route('/create'),
            'view' => Pages\ViewQuote::route('/{record}'),
            'edit' => Pages\EditQuote::route('/{record}/edit'),
        ];
    }
}
