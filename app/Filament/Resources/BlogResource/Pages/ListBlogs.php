<?php

namespace App\Filament\Resources\BlogResource\Pages;

use App\Filament\Resources\BlogResource;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListBlogs extends ListRecords
{
    protected static string $resource = BlogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'All' => Tab::make('All'),
            "Published" => Tab::make('Publish')->modifyQueryUsing(function (Builder $query) {
                $query->where('published', true);
            }),
            "Un Published" => Tab::make('Un Publish')->modifyQueryUsing(function (Builder $query) {
                $query->where('published', false);
            }),
        ];
    }
}
