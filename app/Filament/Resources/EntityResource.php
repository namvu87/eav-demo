<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EntityResource\Pages;
use App\Models\Entity;
use App\Models\EntityType;
use App\Models\Attribute;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class EntityResource extends Resource
{
    protected static ?string $model = Entity::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Entities';
    protected static ?string $navigationGroup = 'EAV System';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Core Fields')
                ->schema([
                    Forms\Components\Select::make('entity_type_id')
                        ->label('Entity Type')
                        ->options(EntityType::active()->pluck('type_name', 'entity_type_id'))
                        ->required()
                        ->reactive()
                        ->afterStateUpdated(fn ($state, Forms\Set $set) =>
                        $set('entity_code', self::generateEntityCode($state))
                        )
                        ->searchable()
                        ->columnSpan(1),

                    Forms\Components\TextInput::make('entity_code')
                        ->label('Entity Code')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(100)
                        ->helperText('e.g., HS-001, ZN-COOK-01')
                        ->columnSpan(1),

                    Forms\Components\TextInput::make('entity_name')
                        ->label('Entity Name')
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull(),

                    Forms\Components\Select::make('parent_id')
                        ->label('Parent Entity')
                        ->options(fn (Forms\Get $get) =>
                        Entity::where('entity_type_id', $get('entity_type_id'))
                            ->pluck('entity_name', 'entity_id')
                        )
                        ->searchable()
                        ->nullable()
                        ->helperText('Leave empty for root entity')
                        ->columnSpan(1),

                    Forms\Components\Textarea::make('description')
                        ->label('Description')
                        ->rows(3)
                        ->maxLength(65535)
                        ->columnSpanFull(),

                    Forms\Components\Toggle::make('is_active')
                        ->label('Active')
                        ->default(true)
                        ->columnSpan(1),

                    Forms\Components\TextInput::make('sort_order')
                        ->label('Sort Order')
                        ->numeric()
                        ->default(0)
                        ->columnSpan(1),
                ])->columns(2),

            // DYNAMIC ATTRIBUTES SECTION
            Forms\Components\Section::make('Attributes')
                ->schema(fn (Forms\Get $get): array =>
                self::getDynamicAttributeFields($get('entity_type_id'))
                )
                ->collapsed(false),

            Forms\Components\Section::make('Metadata')
                ->schema([
                    Forms\Components\KeyValue::make('metadata')
                        ->label('Additional Metadata (JSON)')
                        ->columnSpanFull(),
                ])
                ->collapsed(),
        ]);
    }

    protected static function getDynamicAttributeFields(?int $entityTypeId): array
    {
        if (!$entityTypeId) {
            return [
                Forms\Components\Placeholder::make('select_type')
                    ->label('')
                    ->content('Please select an Entity Type first to see available attributes.'),
            ];
        }

        $attributes = Attribute::where(function($query) use ($entityTypeId) {
            $query->where('entity_type_id', $entityTypeId)
                ->orWhereNull('entity_type_id');
        })
            ->orderBy('sort_order')
            ->get();

        if ($attributes->isEmpty()) {
            return [
                Forms\Components\Placeholder::make('no_attributes')
                    ->label('')
                    ->content('No attributes defined for this Entity Type yet.'),
            ];
        }

        $fields = [];

        foreach ($attributes as $attribute) {
            $field = self::createFieldForAttribute($attribute);

            if ($field) {
                $fields[] = $field;
            }
        }

        return $fields;
    }

    protected static function createFieldForAttribute(Attribute $attribute)
    {
        $fieldName = 'attr_' . $attribute->attribute_id;

        $field = match($attribute->frontend_input) {
            'text' => Forms\Components\TextInput::make($fieldName),
            'textarea' => Forms\Components\Textarea::make($fieldName)->rows(3),
            'select' => Forms\Components\Select::make($fieldName)
                ->options(self::getAttributeOptions($attribute)),
            'multiselect' => Forms\Components\Select::make($fieldName)
                ->multiple()
                ->options(self::getAttributeOptions($attribute)),
            'date' => Forms\Components\DatePicker::make($fieldName),
            'datetime' => Forms\Components\DateTimePicker::make($fieldName),
            'yesno' => Forms\Components\Toggle::make($fieldName),
            'file' => Forms\Components\FileUpload::make($fieldName)
                ->maxFiles($attribute->max_file_count ?? 1),
            default => Forms\Components\TextInput::make($fieldName),
        };

        $field = $field
            ->label($attribute->attribute_label)
            ->required($attribute->is_required);

        if ($attribute->placeholder) {
            $field = $field->placeholder($attribute->placeholder);
        }

        if ($attribute->help_text) {
            $field = $field->helperText($attribute->help_text);
        }

        if ($attribute->default_value) {
            $field = $field->default($attribute->default_value);
        }

        return $field;
    }

    protected static function getAttributeOptions(Attribute $attribute): array
    {
        $options = [];

        foreach ($attribute->options as $option) {
            // Lấy value đầu tiên từ relationship values
            $optionValue = $option->values()->first();
            if ($optionValue) {
                $options[$option->option_id] = $optionValue->value;
            }
        }

        return $options;
    }

    protected static function generateEntityCode(?int $entityTypeId): string
    {
        if (!$entityTypeId) {
            return '';
        }

        $entityType = EntityType::find($entityTypeId);
        if (!$entityType || !$entityType->code_prefix) {
            return '';
        }

        $lastEntity = Entity::where('entity_type_id', $entityTypeId)
            ->orderBy('entity_id', 'desc')
            ->first();

        $nextNumber = $lastEntity ?
            ((int) substr($lastEntity->entity_code, strlen($entityType->code_prefix) + 1) + 1) :
            1;

        return $entityType->code_prefix . '-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('entity_id')
                    ->label('ID')
                    ->sortable(),

                Tables\Columns\TextColumn::make('entityType.icon')
                    ->label('')
                    ->html()
                    ->formatStateUsing(fn ($state) => $state ? "<span style='font-size:20px'>$state</span>" : '')
                    ->width(40),

                Tables\Columns\TextColumn::make('entity_code')
                    ->label('Code')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('bold')
                    ->badge()
                    ->color(fn ($record) => $record->entityType->color ?? 'gray'),

                Tables\Columns\TextColumn::make('entity_name')
                    ->label('Name')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->formatStateUsing(function ($state, $record) {
                        $indent = str_repeat('— ', max(0, (int)($record->level ?? 0)));
                        return $indent . $state;
                    }),

                Tables\Columns\TextColumn::make('entityType.type_name')
                    ->label('Type')
                    ->sortable()
                    ->badge(),

                Tables\Columns\TextColumn::make('parent.entity_name')
                    ->label('Parent')
                    ->sortable()
                    ->toggleable()
                    ->limit(30),

                Tables\Columns\TextColumn::make('level')
                    ->label('Level')
                    ->sortable()
                    ->alignCenter()
                    ->toggleable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('entity_type_id')
                    ->label('Entity Type')
                    ->options(EntityType::active()->pluck('type_name', 'entity_type_id'))
                    ->searchable(),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active Status'),

                Tables\Filters\Filter::make('has_parent')
                    ->label('Has Parent')
                    ->query(fn ($query) => $query->whereNotNull('parent_id')),

                Tables\Filters\Filter::make('root_only')
                    ->label('Root Only')
                    ->query(fn ($query) => $query->whereNull('parent_id')),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('move')
                    ->label('Move')
                    ->icon('heroicon-o-arrows-right-left')
                    ->form([
                        Forms\Components\Select::make('new_parent_id')
                            ->label('New Parent')
                            ->placeholder('Root (no parent)')
                            ->nullable()
                            ->searchable()
                            ->options(function ($record) {
                                // Exclude self and descendants; restrict to same type
                                $excludeIds = $record->getDescendants()->pluck('entity_id')->push($record->entity_id)->all();
                                return Entity::where('entity_type_id', $record->entity_type_id)
                                    ->whereNotIn('entity_id', $excludeIds)
                                    ->orderBy('entity_name')
                                    ->pluck('entity_name', 'entity_id');
                            }),
                    ])
                    ->action(function (Entity $record, array $data) {
                        app(\App\Services\EavService::class)->moveEntity($record, $data['new_parent_id'] ?? null);
                    })
                    ->successNotificationTitle('Entity moved successfully'),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('path', 'asc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEntities::route('/'),
            'create' => Pages\CreateEntity::route('/create'),
            'edit' => Pages\EditEntity::route('/{record}/edit'),
            'view' => Pages\ViewEntity::route('/{record}'),
        ];
    }
}
