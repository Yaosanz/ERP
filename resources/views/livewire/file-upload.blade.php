<div>
    <form wire:submit.prevent="upload">
        @if (session()->has('message'))
            <div class="alert alert-success">
                {{ session('message') }}
            </div>
        @endif

        <input type="file" wire:model="file">
        
        @error('file') <span class="text-danger">{{ $message }}</span> @enderror

        <button type="submit">Upload File</button>
    </form>
</div>
