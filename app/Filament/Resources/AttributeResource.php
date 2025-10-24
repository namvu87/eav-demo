<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AttributeResource\Pages;
use App\Models\Attribute;
use App\Models\EntityType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AttributeResource extends Resource
{
    protected static ?string $model = Attribute::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Attributes';
    protected static ?string $navigationGroup = 'EAV System';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Basic Information')
                ->schema([
                    Forms\Components\Select::make('entity_type_id')
                        ->label('Entity Type')
                        ->options(EntityType::active()->pluck('type_name', 'entity_type_id'))
                        ->searchable()
                        ->helperText('Leave empty for shared attributes')
                        ->nullable()
                        ->reactive()
                        ->columnSpan(1),

                    Forms\Components\TextInput::make('attribute_code')
                        ->label('Attribute Code')
                        ->required()
                        ->regex('/^[a-z0-9_]+$/')
                        ->helperText('e.g., address, phone, capacity')
                        ->maxLength(100)
                        ->columnSpan(1),

                    Forms\Components\TextInput::make('attribute_label')
                        ->label('Label')
                        ->required()
                        ->maxLength(255)
                        ->helperText('Display label')
                        ->columnSpanFull(),
                ])->columns(2),

            Forms\Components\Section::make('Data Type & Input')
                ->schema([
                    Forms\Components\Select::make('backend_type')
                        ->label('Backend Type (Storage)')
                        ->options([
                            'varchar' => 'VARCHAR (Text < 255)',
                            'text' => 'TEXT (Long text)',
                            'int' => 'INTEGER',
                            'decimal' => 'DECIMAL',
                            'datetime' => 'DATETIME',
                            'file' => 'FILE',
                        ])
                        ->required()
                        ->reactive()
                        ->columnSpan(1),

                    Forms\Components\Select::make('frontend_input')
                        ->label('Frontend Input Type')
                        ->options([
                            'text' => 'Text Input',
                            'textarea' => 'Textarea',
                            'select' => 'Select Dropdown',
                            'multiselect' => 'Multiple Select',
                            'date' => 'Date Picker',
                            'datetime' => 'DateTime Picker',
                            'yesno' => 'Yes/No Toggle',
                            'file' => 'File Upload',
                        ])
                        ->required()
                        ->reactive()
                        ->columnSpan(1),
                ])->columns(2),

            Forms\Components\Section::make('Validation & Rules')
                ->schema([
                    Forms\Components\Toggle::make('is_required')
                        ->label('Required')
                        ->default(false)
                        ->columnSpan(1),

                    Forms\Components\Toggle::make('is_unique')
                        ->label('Unique Value')
                        ->default(false)
                        ->columnSpan(1),

                    Forms\Components\Toggle::make('is_searchable')
                        ->label('Searchable')
                        ->default(true)
                        ->columnSpan(1),

                    Forms\Components\Toggle::make('is_filterable')
                        ->label('Filterable')
                        ->default(false)
                        ->columnSpan(1),

                    Forms\Components\TextInput::make('default_value')
                        ->label('Default Value')
                        ->maxLength(65535)
                        ->columnSpanFull(),

                    Forms\Components\KeyValue::make('validation_rules')
                        ->label('Validation Rules (JSON)')
                        ->helperText('e.g., {"min": 5, "max": 100, "regex": "..."}')
                        ->columnSpanFull(),
                ])->columns(4),

            Forms\Components\Section::make('File Upload Settings')
                ->schema([
                    Forms\Components\TextInput::make('max_file_count')
                        ->label('Max File Count')
                        ->numeric()
                        ->default(1)
                        ->columnSpan(1),

                    Forms\Components\TextInput::make('allowed_extensions')
                        ->label('Allowed Extensions')
                        ->helperText('e.g., jpg,png,pdf,dwg')
                        ->maxLength(255)
                        ->columnSpan(1),

                    Forms\Components\TextInput::make('max_file_size_kb')
                        ->label('Max File Size (KB)')
                        ->numeric()
                        ->helperText('e.g., 2048 for 2MB')
                        ->columnSpan(1),
                ])
                ->columns(3)
                ->visible(fn (Forms\Get $get) => $get('backend_type') === 'file'),

            Forms\Components\Section::make('UI Configuration')
                ->schema([
                    Forms\Components\TextInput::make('placeholder')
                        ->label('Placeholder Text')
                        ->maxLength(255)
                        ->columnSpan(1),

                    Forms\Components\Textarea::make('help_text')
                        ->label('Help Text')
                        ->rows(2)
                        ->maxLength(65535)
                        ->columnSpan(1),

                    Forms\Components\TextInput::make('frontend_class')
                        ->label('CSS Class')
                        ->helperText('e.g., col-md-6')
                        ->maxLength(100)
                        ->columnSpan(1),

                    Forms\Components\TextInput::make('sort_order')
                        ->label('Sort Order')
                        ->numeric()
                        ->default(0)
                        ->columnSpan(1),
                ])->columns(2),

            Forms\Components\Section::make('Options (for Select/Multiselect)')
                ->schema([
                    Forms\Components\Repeater::make('options')
                        ->relationship('options')
                        ->schema([
                            Forms\Components\TextInput::make('value')
                                ->label('Option Label')
                                ->required()
                                ->columnSpan(2),

                            Forms\Components\TextInput::make('sort_order')
                                ->label('Order')
                                ->numeric()
                                ->default(0)
                                ->columnSpan(1),

                            Forms\Components\Toggle::make('is_default')
                                ->label('Default')
                                ->default(false)
                                ->columnSpan(1),
                        ])
                        ->columns(4)
                        ->defaultItems(0)
                        ->addActionLabel('Add Option')
                        ->columnSpanFull(),
                ])
                ->visible(fn (Forms\Get $get) => in_array($get('frontend_input'), ['select', 'multiselect']))
                ->collapsed(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('attribute_id')
                    ->label('ID')
                    ->sortable(),

                Tables\Columns\TextColumn::make('entityType.type_name')
                    ->label('Entity Type')
                    ->sortable()
                    ->searchable()
                    ->default('Shared')
                    ->badge()
                    ->color(fn ($record) => $record->entity_type_id ? 'success' : 'warning'),

                Tables\Columns\TextColumn::make('attribute_code')
                    ->label('Code')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('attribute_label')
                    ->label('Label')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('backend_type')
                    ->label('Storage')
                    ->badge()
                    ->color(fn ($state) => match($state) {
                        'varchar' => 'info',
                        'text' => 'primary',
                        'int' => 'success',
                        'decimal' => 'warning',
                        'datetime' => 'danger',
                        'file' => 'gray',
                    }),

                Tables\Columns\TextColumn::make('frontend_input')
                    ->label('Input Type')
                    ->badge(),

                Tables\Columns\IconColumn::make('is_required')
                    ->label('Required')
                    ->boolean()
                    ->alignCenter(),

                Tables\Columns\IconColumn::make('is_searchable')
                    ->label('Searchable')
                    ->boolean()
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Order')
                    ->sortable()
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('entity_type_id')
                    ->label('Entity Type')
                    ->options(EntityType::active()->pluck('type_name', 'entity_type_id'))
                    ->placeholder('All Types'),

                Tables\Filters\SelectFilter::make('backend_type')
                    ->options([
                        'varchar' => 'VARCHAR',
                        'text' => 'TEXT',
                        'int' => 'INTEGER',
                        'decimal' => 'DECIMAL',
                        'datetime' => 'DATETIME',
                        'file' => 'FILE',
                    ]),

                Tables\Filters\TernaryFilter::make('is_required')
                    ->label('Required'),

                Tables\Filters\TernaryFilter::make('is_searchable')
                    ->label('Searchable'),
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
            ])
            ->defaultSort('sort_order', 'asc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAttributes::route('/'),
            'create' => Pages\CreateAttribute::route('/create'),
            'edit' => Pages\EditAttribute::route('/{record}/edit'),
        ];
    }
}
