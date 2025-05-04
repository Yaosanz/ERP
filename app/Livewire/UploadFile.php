<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;

class UploadFile extends Component
{
    use WithFileUploads;

    public $image;

    public function uploadImage()
    {
        // Validasi file
        $this->validate([
            'image' => 'image|max:2048', // Max size 1MB
        ]);

        // Simpan file ke disk 'public'
        // $path = $this->image->store('images', 'public');

        // Opsional: Simpan path di database jika perlu
        // Example: Image::create(['path' => $path]);

        session()->flash('message', 'Image uploaded successfully!');
    }

    public function render()
    {
        return view('livewire.upload-file');
    }
}

