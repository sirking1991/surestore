<?php

namespace App\Filament\Resources\ProductionResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class WorkOrdersRelationManager extends RelationManager
{
    protected static string $relationship = 'workOrders';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('order_number')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->disabled(fn ($record) => $record !== null)
                    ->default(function () {
                        // Generate a unique work order number with format WO-YYYYMMDD-XXXX
                        $date = now()->format('Ymd');
                        $latestOrder = \App\Models\WorkOrder::whereDate('created_at', today())
                            ->latest()
                            ->first();
                        
                        $sequence = $latestOrder 
                            ? (int) substr($latestOrder->order_number, -4) + 1 
                            : 1;
                        
                        return 'WO-' . $date . '-' . str_pad($sequence, 4, '0', STR_PAD_LEFT);
                    }),
                Forms\Components\DatePicker::make('order_date')
                    ->required()
                    ->default(now()),
                Forms\Components\Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'in_progress' => 'In Progress',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ])
                    ->required()
                    ->default('pending'),
                Forms\Components\DatePicker::make('due_date')
                    ->required()
                    ->default(now()->addDays(7)),
                Forms\Components\Textarea::make('notes')
                    ->maxLength(65535)
                    ->columnSpanFull(),
                Forms\Components\Select::make('assigned_to')
                    ->relationship('assignedTo', 'first_name')
                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->first_name} {$record->last_name}")
                    ->searchable()
                    ->preload()
                    ->label('Assigned To'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('order_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('due_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'pending',
                        'primary' => 'in_progress',
                        'success' => 'completed',
                        'danger' => 'cancelled',
                    ]),
                Tables\Columns\TextColumn::make('assignedTo.first_name')
                    ->label('Assigned To')
                    ->formatStateUsing(fn ($record) => $record->assignedTo ? "{$record->assignedTo->first_name} {$record->assignedTo->last_name}" : '-')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'in_progress' => 'In Progress',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ]),
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
