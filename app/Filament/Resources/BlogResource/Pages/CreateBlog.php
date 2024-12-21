<?php

namespace App\Filament\Resources\BlogResource\Pages;

use App\Filament\Resources\BlogResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Storage;

class CreateBlog extends CreateRecord
{
    protected static string $resource = BlogResource::class;
    protected static ?string $title = 'Buat Data Blog';
    protected function getRedirectUrl(): string
    {
        return static::$resource::getUrl('index');
    }

    public function uploadThumbnail($file)
    {
        // Tentukan lokasi penyimpanan
        $path = $file->store('public/blogs');
        
        // Dapatkan URL file yang di-upload
        return Storage::url($path);
    }
}
