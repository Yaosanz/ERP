<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BlogResource\Pages;
use App\Models\Blog;
use Filament\Forms;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

use Illuminate\Support\Str;
use function Livewire\on;

class BlogResource extends Resource
{
    protected static ?string $model = Blog::class;
    protected static ?string $navigationGroup = "Konten Blog";
    protected static ?string $navigationLabel = 'Kelola Blog';
    protected static ?int $navigationSort = 4;
    protected static ?string $navigationIcon = 'carbon-blog';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Detail Blog')
                    ->description('Isi form berikut untuk menambahkan konten blog baru.')
                    ->collapsible()
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Judul')
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (?string $state, Forms\Set $set) {
                                if (!empty($state)) {
                                    $set('slug', Str::slug($state));
                                }
                            })
                            ->required()
                            ->maxLength(100),
                        Forms\Components\TextInput::make('slug')
                            ->label('URL Slug')
                            ->required()
                            ->disabled()
                            ->maxLength(100),
                        MarkdownEditor::make('content')
                            ->label('Deskripsi Konten')
                            ->required()
                            ->columnSpan('full')
                            ->maxLength(255),
                    ])
                    ->columnSpan(1)
                    ->columns(2),

                Section::make('Unggahan Blog')
                    ->description('Unggah thumbnail blog untuk menarik perhatian pembaca.')
                    ->collapsible()
                    ->schema([
                        Forms\Components\FileUpload::make('thumbnail')
                            ->label('Thumbnail')
                            ->disk('public')
                            ->directory('thumbnails')
                            ->image()
                            ->imageEditor()
                            ->downloadable()
                            ->previewable()
                            ,
                    ])
                    ->columnSpan(1),

                Section::make('Tag & Status')
                    ->description('Masukkan tag blog dan tentukan apakah blog akan diterbitkan.')
                    ->collapsible()
                    ->schema([
                        Forms\Components\TagsInput::make('tags')
                            ->label('Penanda')
                            ->placeholder('Masukkan tag')
                            ->required(),
                        ToggleButtons::make('published')
                            ->label('Publikasikan')
                            ->boolean()
                            ->grouped()
                            ->default(false)
                            ->required(),
                    ])
                    ->columnSpan(1),
            ])
            ->columns(3); 
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Judul')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('slug')
                    ->label('URL Slug')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('tags')
                    ->label('Penanda')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\CheckboxColumn::make('published')
                    ->label('Dipublikasikan'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal Dibuat')
                    ->dateTime()
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Tanggal Diubah')
                    ->dateTime()
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // Filters can be added here
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            // Define relations if needed
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBlogs::route('/'),
            'create' => Pages\CreateBlog::route('/create'),
            'edit' => Pages\EditBlog::route('/{record}/edit'),
        ];
    }
}
