<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WorkOrderResource\Pages;
use App\Filament\Resources\WorkOrderResource\RelationManagers;
use App\Models\WorkOrder;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class WorkOrderResource extends Resource
{
    protected static ?string $model = WorkOrder::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationGroup = 'Manufacturing';
    
    protected static ?int $navigationSort = 20;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Work Order Information')
                    ->schema([
                        Forms\Components\TextInput::make('order_number')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->disabled(fn ($record) => $record !== null)
                            ->default(function () {
                                // Generate a unique work order number with format WO-YYYYMMDD-XXXX
                                $date = now()->format('Ymd');
                                $latestOrder = WorkOrder::whereDate('created_at', today())
                                    ->latest()
                                    ->first();
                                
                                $sequence = $latestOrder 
                                    ? (int) substr($latestOrder->order_number, -4) + 1 
                                    : 1;
                                
                                return 'WO-' . $date . '-' . str_pad($sequence, 4, '0', STR_PAD_LEFT);
                            }),
                        Forms\Components\Select::make('production_id')
                            ->relationship('production', 'batch_number')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\DatePicker::make('scheduled_date')
                            ->required()
                            ->default(now()),
                        Forms\Components\Select::make('status')
                            ->options([
                                'draft' => 'Draft',
                                'scheduled' => 'Scheduled',
                                'in_progress' => 'In Progress',
                                'completed' => 'Completed',
                                'cancelled' => 'Cancelled',
                            ])
                            ->required()
                            ->default('draft'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Time Information')
                    ->schema([
                        Forms\Components\DateTimePicker::make('start_time'),
                        Forms\Components\DateTimePicker::make('end_time'),
                        Forms\Components\TextInput::make('estimated_minutes')
                            ->numeric()
                            ->minValue(0)
                            ->label('Estimated Duration (minutes)'),
                        Forms\Components\TextInput::make('actual_minutes')
                            ->numeric()
                            ->minValue(0)
                            ->label('Actual Duration (minutes)'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Assignment')
                    ->schema([
                        Forms\Components\Select::make('assigned_to')
                            ->relationship('assignedTo', 'first_name')
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->first_name} {$record->last_name}")
                            ->searchable()
                            ->preload()
                            ->label('Assigned To'),
                        Forms\Components\Hidden::make('user_id')
                            ->default(fn () => Auth::id())
                            ->required(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Notes')
                    ->schema([
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
                Tables\Columns\TextColumn::make('order_number')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('production.batch_number')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('scheduled_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'gray' => 'draft',
                        'warning' => 'scheduled',
                        'primary' => 'in_progress',
                        'success' => 'completed',
                        'danger' => 'cancelled',
                    ]),
                Tables\Columns\TextColumn::make('items_completion_percentage')
                    ->label('Completion')
                    ->formatStateUsing(fn ($state) => $state . '%')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_overdue')
                    ->boolean()
                    ->trueIcon('heroicon-o-exclamation-circle')
                    ->falseIcon('')
                    ->label('Overdue')
                    ->sortable(),
                Tables\Columns\TextColumn::make('assignedTo.first_name')
                    ->label('Assigned To')
                    ->formatStateUsing(fn ($record) => $record->assignedTo ? "{$record->assignedTo->first_name} {$record->assignedTo->last_name}" : '-')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('start_time')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('end_time')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('estimated_minutes')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('actual_minutes')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'scheduled' => 'Scheduled',
                        'in_progress' => 'In Progress',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ]),
                Tables\Filters\SelectFilter::make('production_id')
                    ->relationship('production', 'batch_number')
                    ->searchable()
                    ->preload()
                    ->label('Production'),
                Tables\Filters\SelectFilter::make('assigned_to')
                    ->relationship('assignedTo', 'first_name')
                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->first_name} {$record->last_name}")
                    ->searchable()
                    ->preload()
                    ->label('Assigned To'),
                Tables\Filters\Filter::make('scheduled_date')
                    ->form([
                        Forms\Components\DatePicker::make('scheduled_from'),
                        Forms\Components\DatePicker::make('scheduled_until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['scheduled_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('scheduled_date', '>=', $date),
                            )
                            ->when(
                                $data['scheduled_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('scheduled_date', '<=', $date),
                            );
                    }),
                Tables\Filters\Filter::make('overdue')
                    ->query(fn (Builder $query): Builder => $query->whereDate('scheduled_date', '<', now())
                        ->whereNotIn('status', ['completed', 'cancelled']))
                    ->label('Overdue Work Orders'),
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
            'index' => Pages\ListWorkOrders::route('/'),
            'create' => Pages\CreateWorkOrder::route('/create'),
            'view' => Pages\ViewWorkOrder::route('/{record}'),
            'edit' => Pages\EditWorkOrder::route('/{record}/edit'),
        ];
    }
}
