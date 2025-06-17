<?php

namespace App\Filament\Resources\WorkOrderResource\RelationManagers;

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
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('product_id')
                    ->relationship('product', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Product (Optional)'),
                Forms\Components\TextInput::make('description')
                    ->maxLength(255)
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('quantity')
                    ->numeric()
                    ->default(1),
                Forms\Components\TextInput::make('unit')
                    ->maxLength(50),
                Forms\Components\TextInput::make('sequence_number')
                    ->numeric()
                    ->default(fn ($livewire) => $livewire->ownerRecord->items()->count() + 1)
                    ->label('Sequence'),
                Forms\Components\Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'in_progress' => 'In Progress',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ])
                    ->required()
                    ->default('pending'),
                Forms\Components\TextInput::make('estimated_minutes')
                    ->numeric()
                    ->label('Est. Minutes'),
                Forms\Components\TextInput::make('actual_minutes')
                    ->numeric()
                    ->label('Actual Minutes'),
                Forms\Components\DateTimePicker::make('started_at')
                    ->label('Started At'),
                Forms\Components\DateTimePicker::make('completed_at')
                    ->label('Completed At'),
                Forms\Components\Select::make('assigned_to')
                    ->relationship('assignedUser', 'first_name')
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
                Tables\Columns\TextColumn::make('sequence_number')
                    ->sortable()
                    ->label('#'),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('product.name')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('quantity')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('unit')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'pending',
                        'primary' => 'in_progress',
                        'success' => 'completed',
                        'danger' => 'cancelled',
                    ]),
                Tables\Columns\TextColumn::make('completion_percentage')
                    ->label('Completion')
                    ->formatStateUsing(fn ($state) => $state . '%')
                    ->sortable(),
                Tables\Columns\TextColumn::make('estimated_minutes')
                    ->label('Est. Min')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('actual_minutes')
                    ->label('Act. Min')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('assignedUser.first_name')
                    ->label('Assigned To')
                    ->formatStateUsing(fn ($record) => $record->assignedUser ? "{$record->assignedUser->first_name} {$record->assignedUser->last_name}" : '-')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('started_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('completed_at')
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
                Tables\Filters\SelectFilter::make('assigned_to')
                    ->relationship('assignedUser', 'first_name')
                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->first_name} {$record->last_name}")
                    ->searchable()
                    ->preload()
                    ->label('Assigned To'),
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
            ])
            ->defaultSort('sequence_number');
    }
}
