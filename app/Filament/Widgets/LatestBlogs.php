<?php

namespace App\Filament\Widgets;

use App\Models\Blog;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;

class LatestBlogs extends BaseWidget
{
    protected static ?int $sort = 5;
    protected int | string | array $columnSpan = 'full';
    protected static ?string $title = 'Kelola Blog';
    protected static bool $isLazy = false;
    public function table(Table $table): Table
    {
        return $table
            ->query(Blog::query())
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('slug')
                    ->label('URL Slug')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('content')
                    ->label('Deskripsi Konten')
                    ->limit(50)
                    ->placeholder('No description.')
                    ->searchable(),

                Tables\Columns\TextColumn::make('tags')
                    ->label('Penanda')
                    ->formatStateUsing(fn ($state) => is_array($state) ? implode(', ', $state) : $state)
                    ->searchable(),

                Tables\Columns\TextColumn::make('published')
                    ->label('Dipublikasikan')
                    ->badge(fn ($record) => $record->published ? 'Publish' : 'Unpublish')
                    ->sortable()
                    ->searchable()
                    ->color(fn ($record) => $record->published ? 'success' : 'danger')
                    ->formatStateUsing(fn ($state) => $state ? 'Publish' : 'Unpublish'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal Dibuat')
                    ->dateTime()
                    ->sortable(),
            ])
            ->actions([
                Action::make('publish')
                    ->action(function (Blog $record) {
                        $record->published = true;
                        $record->save();
                    })
                    ->hidden(fn (Blog $record): bool => $record->published),

                Action::make('unpublish')
                    ->action(function (Blog $record) {
                        $record->published = false;
                        $record->save();
                    })
                    ->visible(fn (Blog $record): bool => $record->published),
            ])
            ->filters([
                Filter::make('published')
                    ->query(fn (Builder $query) => $query->where('published', true))
                    ->label('Published'),

                Filter::make('unpublished')
                    ->query(fn (Builder
                    $query) => $query->where('published', false))
                    ->label('Unpublished'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
