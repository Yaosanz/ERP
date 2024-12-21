<?php

namespace App\Filament\Resources\BlogResource\Api\Transformers;

use App\Models\Blog;

class BlogTransformer
{
    public function transform(Blog $blog)
    {
        return [
            'id' => $blog->id,
            'title' => $blog->title,
            'slug' => $blog->slug,
            'content' => $blog->content,
            'thumbnail' => $blog->thumbnail ? config('app.url') . '/storage/' . $blog->thumbnail : null,
            'tags' => $blog->tags,
            'published' => $blog->published,
            'created_at' => $blog->created_at,
            'updated_at' => $blog->updated_at,
        ];
    }
}