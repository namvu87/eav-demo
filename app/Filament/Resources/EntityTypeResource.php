<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EntityTypeResource\Pages;
use App\Models\EntityType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class EntityTypeResource extends Resource
{
    protected static ?string $model = EntityType::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';
    protected static ?string $navigationLabel = 'Entity Types';
    protected static ?string $navigationGroup = 'EAV System';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Basic Information')
                ->schema([
                    Forms\Components\TextInput::make('type_code')
                        ->label('Type Code')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->regex('/^[a-z0-9_]+$/')
                        ->helperText('Only lowercase, numbers, and underscores (e.g., hospital, zone)')
                        ->maxLength(100)
                        ->columnSpan(1),

                    Forms\Components\TextInput::make('type_name')
                        ->label('Type Name (Vietnamese)')
                        ->required()
                        ->maxLength(255)
                        ->columnSpan(1),

                    Forms\Components\TextInput::make('type_name_en')
                        ->label('Type Name (English)')
                        ->maxLength(255)
                        ->columnSpan(1),

                    Forms\Components\TextInput::make('code_prefix')
                        ->label('Code Prefix')
                        ->helperText('Prefix for entity codes (e.g., HS, ZN, DP)')
                        ->maxLength(10)
                        ->columnSpan(1),
                ])->columns(2),

            Forms\Components\Section::make('Display Configuration')
                ->schema([
                    Forms\Components\TextInput::make('icon')
                        ->label('Icon')
                        ->helperText('Icon class or emoji (e.g., 🏥, heroicon-o-building)')
                        ->maxLength(100)
                        ->columnSpan(1),

                    Forms\Components\ColorPicker::make('color')
                        ->label('Color')
                        ->helperText('Display color for this type')
                        ->columnSpan(1),
                ])->columns(2),

            Forms\Components\Section::make('Description & Config')
                ->schema([
                    Forms\Components\Textarea::make('description')
                        ->label('Description')
                        ->rows(3)
                        ->maxLength(65535)
                        ->columnSpanFull(),

                    Forms\Components\KeyValue::make('config')
                        ->label('Configuration (JSON)')
                        ->helperText('Custom configuration options')
                        ->columnSpanFull(),
                ])->collapsed(),

            Forms\Components\Section::make('Settings')
                ->schema([
                    Forms\Components\TextInput::make('sort_order')
                        ->label('Sort Order')
                        ->numeric()
                        ->default(0)
                        ->columnSpan(1),

                    Forms\Components\Toggle::make('is_system')
                        ->label('System Type')
                        ->helperText('System types cannot be deleted')
                        ->default(false)
                        ->columnSpan(1),

                    Forms\Components\Toggle::make('is_active')
                        ->label('Active')
                        ->default(true)
                        ->columnSpan(1),
                ])->columns(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('entity_type_id')
                    ->label('ID')
                    ->sortable(),

                Tables\Columns\TextColumn::make('icon')
                    ->label('')
                    ->html()
                    ->formatStateUsing(fn ($state) => $state ? "<span style='font-size:24px'>$state</span>" : '')
                    ->width(50),

                Tables\Columns\TextColumn::make('type_code')
                    ->label('Code')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->badge()
                    ->color(fn ($record) => $record->color ?? 'gray'),

                Tables\Columns\TextColumn::make('type_name')
                    ->label('Name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('code_prefix')
                    ->label('Prefix')
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('entities_count')
                    ->label('Entities')
                    ->counts('entities')
                    ->sortable()
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('attributes_count')
                    ->label('Attributes')
                    ->counts('attributes')
                    ->sortable()
                    ->alignCenter(),

                Tables\Columns\IconColumn::make('is_system')
                    ->label('System')
                    ->boolean()
                    ->alignCenter(),

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
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active'),
                Tables\Filters\TernaryFilter::make('is_system')
                    ->label('System Types'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->before(function ($record) {
                        if ($record->is_system) {
                            throw new \Exception('Cannot delete system entity types');
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('sort_order', 'asc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEntityTypes::route('/'),
            'create' => Pages\CreateEntityType::route('/create'),
            'edit' => Pages\EditEntityType::route('/{record}/edit'),
        ];
    }
}
