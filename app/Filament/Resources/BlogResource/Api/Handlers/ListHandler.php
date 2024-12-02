<?php

namespace App\Filament\Resources\BlogResource\Api\Handlers;

use App\Filament\Resources\BlogResource;
use Rupadana\ApiService\Http\Handlers;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Http\Request;

class ListHandler extends Handlers
{

    public static bool $public = true;
    public static string | null $uri = '/blogs';
    public static string | null $resource = BlogResource::class;

    public function handler(Request $request)
    {
        $query = static::getEloquentQuery();

        $blogs = QueryBuilder::for($query)
            ->get();

        return response()->json($blogs);
    }
} 