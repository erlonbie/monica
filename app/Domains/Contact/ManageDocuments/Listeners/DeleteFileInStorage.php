<?php

namespace App\Domains\Contact\ManageDocuments\Listeners;

use App\Domains\Contact\ManageDocuments\Events\FileDeleted;
use App\Models\File;
use Illuminate\Support\Facades\Storage;

class DeleteFileInStorage
{
    /**
     * The file instance.
     */
    public File $file;

    /**
     * Handle the event.
     */
    public function handle(FileDeleted $event)
    {
        $this->file = $event->file;
        $this->deleteFile();
    }

    private function deleteFile(): void
    {
        $extension = pathinfo($this->file->name, PATHINFO_EXTENSION);
        $path = 'photos/' . $this->file->uuid;

        if ($extension) {
            $path .= '.' . $extension;
        }

        Storage::disk('s3')->delete($path);
    }
}
