<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;

class FileUpload extends Component
{
    use WithFileUploads;

    public $file;

    public function upload()
    {
        $this->validate([
            'file' => 'required|file|max:10240', // maksimal 10MB
        ]);

        $this->file->store('uploads', 'public');
        session()->flash('message', 'File successfully uploaded!');
    }

    public function render()
    {
        return view('livewire.file-upload');
    }
}
