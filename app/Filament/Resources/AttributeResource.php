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
            // LEFT COLUMN - Main Configuration
            Forms\Components\Section::make('Thông tin cơ bản')
                ->schema([
                    Forms\Components\Select::make('entity_type_id')
                        ->label('Loại thực thể')
                        ->options(EntityType::active()->pluck('type_name', 'entity_type_id'))
                        ->searchable()
                        ->placeholder('Chọn loại thực thể (để trống nếu dùng chung)')
                        ->nullable()
                        ->reactive()
                        ->columnSpanFull(),

                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\TextInput::make('attribute_code')
                                ->label('Mã thuộc tính')
                                ->required()
                                ->regex('/^[a-z0-9_]+$/')
                                ->placeholder('vd: dia_chi, so_dien_thoai')
                                ->helperText('Chỉ chữ thường, số và dấu gạch dưới')
                                ->maxLength(100),

                            Forms\Components\TextInput::make('attribute_label')
                                ->label('Tên hiển thị')
                                ->required()
                                ->placeholder('vd: Địa chỉ, Số điện thoại')
                                ->maxLength(255),
                        ]),

                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\Select::make('backend_type')
                                ->label('Kiểu lưu trữ')
                                ->options([
                                    'varchar' => 'VARCHAR (Văn bản ngắn)',
                                    'text' => 'TEXT (Văn bản dài)',
                                    'int' => 'INTEGER (Số nguyên)',
                                    'decimal' => 'DECIMAL (Số thập phân)',
                                    'datetime' => 'DATETIME (Ngày giờ)',
                                    'file' => 'FILE (Tệp đính kèm)',
                                ])
                                ->required()
                                ->reactive()
                                ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                                    // Auto-set default frontend_input based on backend_type
                                    $defaultInputs = [
                                        'varchar' => 'text',
                                        'text' => 'textarea',
                                        'int' => 'text',
                                        'decimal' => 'text',
                                        'datetime' => 'datetime',
                                        'file' => 'file',
                                    ];

                                    // Chỉ set nếu chưa có giá trị hoặc giá trị hiện tại không hợp lệ
                                    $currentInput = $get('frontend_input');
                                    $validInputs = self::getValidFrontendInputs($state);

                                    if (!$currentInput || !in_array($currentInput, array_keys($validInputs))) {
                                        $set('frontend_input', $defaultInputs[$state] ?? null);
                                    }
                                })
                                ->native(false),

                            Forms\Components\Select::make('frontend_input')
                                ->label('Kiểu nhập liệu')
                                ->options(function (Forms\Get $get) {
                                    $backendType = $get('backend_type');
                                    return self::getValidFrontendInputs($backendType);
                                })
                                ->required()
                                ->reactive()
                                ->native(false)
                                ->disabled(fn (Forms\Get $get) => !$get('backend_type'))
                                ->helperText(fn (Forms\Get $get) =>
                                !$get('backend_type')
                                    ? '⚠️ Vui lòng chọn Kiểu lưu trữ trước'
                                    : 'Chọn cách hiển thị input'
                                ),
                        ]),

                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\TextInput::make('placeholder')
                                ->label('Placeholder')
                                ->placeholder('vd: Nhập địa chỉ...')
                                ->maxLength(255),

                            Forms\Components\TextInput::make('default_value')
                                ->label('Giá trị mặc định')
                                ->maxLength(65535),
                        ]),

                    Forms\Components\Textarea::make('help_text')
                        ->label('Văn bản trợ giúp')
                        ->placeholder('Hướng dẫn điền thông tin...')
                        ->rows(2)
                        ->maxLength(65535)
                        ->columnSpanFull(),

                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\TextInput::make('frontend_class')
                                ->label('CSS Class')
                                ->placeholder('vd: col-md-6')
                                ->maxLength(100),

                            Forms\Components\TextInput::make('sort_order')
                                ->label('Thứ tự sắp xếp')
                                ->numeric()
                                ->default(0)
                                ->minValue(0),
                        ]),
                ])
                ->columnSpan(['lg' => 2]),

            // RIGHT COLUMN - Quick Settings
            Forms\Components\Section::make('Cài đặt nhanh')
                ->schema([
                    Forms\Components\Placeholder::make('quick_settings_help')
                        ->label('')
                        ->content('Tùy chọn xác thực và hiển thị'),

                    Forms\Components\Toggle::make('is_required')
                        ->label('Bắt buộc')
                        ->helperText('Phải nhập giá trị')
                        ->default(false)
                        ->inline(false),

                    Forms\Components\Toggle::make('is_unique')
                        ->label('Giá trị duy nhất')
                        ->helperText('Không được trùng lặp')
                        ->default(false)
                        ->inline(false),

                    Forms\Components\Toggle::make('is_searchable')
                        ->label('Cho phép tìm kiếm')
                        ->default(true)
                        ->inline(false),

                    Forms\Components\Toggle::make('is_filterable')
                        ->label('Cho phép lọc')
                        ->default(false)
                        ->inline(false),

                    Forms\Components\Fieldset::make('Validation Rules')
                        ->label('Quy tắc xác thực')
                        ->schema([
                            Forms\Components\KeyValue::make('validation_rules')
                                ->label('')
                                ->helperText('VD: {"min": 5, "max": 100}')
                                ->columnSpanFull()
                                ->addActionLabel('Thêm quy tắc'),
                        ])
                ])
                ->columnSpan(['lg' => 1]),

            // FULL WIDTH - File Upload Settings
            Forms\Components\Section::make('Cấu hình tải tệp')
                ->schema([
                    Forms\Components\Grid::make(3)
                        ->schema([
                            Forms\Components\TextInput::make('max_file_count')
                                ->label('Số tệp tối đa')
                                ->numeric()
                                ->default(1)
                                ->minValue(1)
                                ->maxValue(10),

                            Forms\Components\TextInput::make('allowed_extensions')
                                ->label('Định dạng cho phép')
                                ->placeholder('jpg,png,pdf,dwg')
                                ->helperText('Cách nhau bởi dấu phẩy')
                                ->maxLength(255),

                            Forms\Components\TextInput::make('max_file_size_kb')
                                ->label('Kích thước tối đa (KB)')
                                ->numeric()
                                ->placeholder('2048')
                                ->helperText('VD: 2048 = 2MB')
                                ->suffix('KB'),
                        ]),
                ])
                ->visible(fn (Forms\Get $get) => $get('backend_type') === 'file')
                ->collapsed()
                ->columnSpanFull(),

            // FULL WIDTH - Options for Select/Multiselect
            Forms\Components\Section::make('Danh sách tùy chọn')
                ->description('Cấu hình các lựa chọn cho Select/Multiselect')
                ->schema([
                    Forms\Components\Repeater::make('options')
                        ->relationship('options')
                        ->schema([
                            Forms\Components\TextInput::make('value')
                                ->label('Nhãn hiển thị')
                                ->required()
                                ->placeholder('VD: Phòng VIP')
                                ->columnSpan(2),

                            Forms\Components\TextInput::make('sort_order')
                                ->label('Thứ tự')
                                ->numeric()
                                ->default(0)
                                ->columnSpan(1),

                            Forms\Components\Toggle::make('is_default')
                                ->label('Mặc định')
                                ->default(false)
                                ->columnSpan(1),
                        ])
                        ->columns(4)
                        ->defaultItems(0)
                        ->addActionLabel('➕ Thêm tùy chọn')
                        ->reorderable()
                        ->collapsible()
                        ->itemLabel(fn (array $state): ?string => $state['value'] ?? null)
                        ->columnSpanFull(),
                ])
                ->visible(fn (Forms\Get $get) => in_array($get('frontend_input'), ['select', 'multiselect']))
                ->collapsed()
                ->columnSpanFull(),
        ])
            ->columns(3); // Total 3 columns: 2 for left, 1 for right
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('attribute_id')
                    ->label('#')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('entityType.type_name')
                    ->label('Loại thực thể')
                    ->sortable()
                    ->searchable()
                    ->default('Dùng chung')
                    ->badge()
                    ->color(fn ($record) => $record->entity_type_id ? 'success' : 'warning'),

                Tables\Columns\TextColumn::make('attribute_code')
                    ->label('Mã thuộc tính')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('bold')
                    ->icon('heroicon-m-code-bracket'),

                Tables\Columns\TextColumn::make('attribute_label')
                    ->label('Tên hiển thị')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => $record->help_text)
                    ->wrap(),

                Tables\Columns\TextColumn::make('backend_type')
                    ->label('Kiểu dữ liệu')
                    ->badge()
                    ->color(fn ($state) => match($state) {
                        'varchar' => 'info',
                        'text' => 'primary',
                        'int' => 'success',
                        'decimal' => 'warning',
                        'datetime' => 'danger',
                        'file' => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => strtoupper($state)),

                Tables\Columns\TextColumn::make('frontend_input')
                    ->label('Kiểu nhập')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match($state) {
                        'text' => '📝 Text',
                        'textarea' => '📄 Textarea',
                        'select' => '📋 Select',
                        'multiselect' => '☑️ Multi',
                        'date' => '📅 Date',
                        'datetime' => '🕐 DateTime',
                        'yesno' => '✅ Toggle',
                        'file' => '📎 File',
                        default => $state,
                    }),

                Tables\Columns\IconColumn::make('is_required')
                    ->label('Bắt buộc')
                    ->boolean()
                    ->alignCenter()
                    ->toggleable(),

                Tables\Columns\IconColumn::make('is_searchable')
                    ->label('Tìm kiếm')
                    ->boolean()
                    ->alignCenter()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('sort_order')
                    ->label('STT')
                    ->sortable()
                    ->alignCenter()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Ngày tạo')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('entity_type_id')
                    ->label('Loại thực thể')
                    ->options(EntityType::active()->pluck('type_name', 'entity_type_id'))
                    ->placeholder('Tất cả')
                    ->searchable(),

                Tables\Filters\SelectFilter::make('backend_type')
                    ->label('Kiểu dữ liệu')
                    ->options([
                        'varchar' => 'VARCHAR',
                        'text' => 'TEXT',
                        'int' => 'INTEGER',
                        'decimal' => 'DECIMAL',
                        'datetime' => 'DATETIME',
                        'file' => 'FILE',
                    ])
                    ->multiple(),

                Tables\Filters\SelectFilter::make('frontend_input')
                    ->label('Kiểu nhập')
                    ->options([
                        'text' => 'Text Input',
                        'textarea' => 'Textarea',
                        'select' => 'Select',
                        'multiselect' => 'Multiselect',
                        'date' => 'Date',
                        'datetime' => 'DateTime',
                        'yesno' => 'Yes/No',
                        'file' => 'File',
                    ])
                    ->multiple(),

                Tables\Filters\TernaryFilter::make('is_required')
                    ->label('Bắt buộc')
                    ->placeholder('Tất cả')
                    ->trueLabel('Có')
                    ->falseLabel('Không'),

                Tables\Filters\TernaryFilter::make('is_searchable')
                    ->label('Tìm kiếm được')
                    ->placeholder('Tất cả')
                    ->trueLabel('Có')
                    ->falseLabel('Không'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Xem')
                    ->icon('heroicon-m-eye'),
                Tables\Actions\EditAction::make()
                    ->label('Sửa')
                    ->icon('heroicon-m-pencil-square'),
                Tables\Actions\DeleteAction::make()
                    ->label('Xóa')
                    ->icon('heroicon-m-trash'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Xóa đã chọn'),
                ]),
            ])
            ->defaultSort('sort_order', 'asc')
            ->striped()
            ->paginated([10, 25, 50, 100]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAttributes::route('/'),
            'create' => Pages\CreateAttribute::route('/create'),
            'edit' => Pages\EditAttribute::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'success';
    }

    /**
     * Get valid frontend inputs based on backend type
     * Mapping logic based on data type compatibility
     */
    protected static function getValidFrontendInputs(?string $backendType): array
    {
        if (!$backendType) {
            return [];
        }

        return match($backendType) {
            'varchar' => [
                'text' => '📝 Text Input',
                'select' => '📋 Select Dropdown',
                'yesno' => '✅ Yes/No Toggle',
            ],
            'text' => [
                'textarea' => '📄 Textarea',
                'multiselect' => '☑️ Multiple Select',
            ],
            'int' => [
                'text' => '📝 Number Input',
                'select' => '📋 Select Dropdown',
                'yesno' => '✅ Yes/No Toggle',
            ],
            'decimal' => [
                'text' => '📝 Decimal Input',
            ],
            'datetime' => [
                'date' => '📅 Date Picker',
                'datetime' => '🕐 DateTime Picker',
            ],
            'file' => [
                'file' => '📎 File Upload',
            ],
            default => [],
        };
    }
}
