<?php
namespace App\Filament\Resources\BlogResource\Api\Handlers;

use Illuminate\Http\Request;
use Rupadana\ApiService\Http\Handlers;
use Spatie\QueryBuilder\QueryBuilder;
use App\Filament\Resources\BlogResource;
use App\Filament\Resources\BlogResource\Api\Transformers\BlogTransformer;
use Illuminate\Container\Attributes\Log;

class PaginationHandler extends Handlers {
    public static string | null $uri = '/';
    public static bool $public = true;
    public static string | null $resource = BlogResource::class;

    public function handler()
    {
        try {
            $query = static::getEloquentQuery();
            
            $paginator = QueryBuilder::for($query)
                ->allowedFields($this->getAllowedFields() ?? [])
                ->allowedSorts($this->getAllowedSorts() ?? [])
                ->allowedFilters($this->getAllowedFilters() ?? [])
                ->allowedIncludes($this->getAllowedIncludes() ?? [])
                ->paginate(request()->query('per_page', 10));

            $transformer = new BlogTransformer();
            
            $transformedData = collect($paginator->items())->map(function ($item) use ($transformer) {
                return $transformer->transform($item);
            });

            return response()->json([
                'data' => $transformedData->values(),
                'pagination' => [
                    'current_page' => $paginator->currentPage(),
                    'last_page' => $paginator->lastPage(),
                    'per_page' => $paginator->perPage(),
                    'total' => $paginator->total()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
