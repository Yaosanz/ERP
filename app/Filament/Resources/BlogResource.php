<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BlogResource\Pages;
use App\Models\Blog;
use Filament\Forms;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BlogResource extends Resource
{
    protected static ?string $model = Blog::class;
    protected static ?string $navigationGroup = "Blog Post";
    protected static ?string $navigationLabel = 'Konten Manajemen';
    protected static ?int $navigationSort = 4;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

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
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->maxLength(255),
                        MarkdownEditor::make('content')
                            ->label('Deskripsi Konten')
                            ->required()
                            ->columnSpan('full')
                            ->maxLength(255),
                    ])
                    ->columnSpan(1)
                    ->columns(2),

                Section::make('Unggahan Blog')
                    ->description('Unggah thumbnail dan tag blog.')
                    ->collapsible()
                    ->schema([
                        Forms\Components\FileUpload::make('thumbnail')
                            ->label('Thumbnail')
                            ->disk('public')
                            ->directory('thumbnail')
                            ->image()
                            ->required(),
                    ])
                    ->columnSpan(1),

                Section::make('Tag & Status')
                    ->description('Masukkan tag blog dan tentukan status terbit.')
                    ->collapsible()
                    ->schema([
                        Forms\Components\TagsInput::make('tags')
                            ->label('Tags')
                            ->placeholder('Masukkan tag')
                            ->required(),
                        Checkbox::make('published')
                            ->label('Terbitkan')
                            ->default(false)
                            ->required(),
                    ])
                    ->columnSpan(1),
            ])
            ->columns(2); // Adjust the layout to be 2 columns
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Judul')
                    ->searchable(),
                Tables\Columns\TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable(),
                Tables\Columns\ImageColumn::make('thumbnail')
                    ->label('Thumbnail')
                    ->sortable(),
                Tables\Columns\TextColumn::make('tags')
                    ->label('Tags')
                    ->searchable(),
                Tables\Columns\CheckboxColumn::make('published')
                    ->label('Diterbitkan'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal Dibuat')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Tanggal Diubah')
                    ->dateTime()
                    ->sortable()
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
