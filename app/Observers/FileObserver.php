<?php

namespace App\Observers;

use App\Models\File;
use Storage;

class FileObserver
{
    /**
     * Handle the file "updating" event.
     *
     * @param  \App\Models\File  $file
     * @return void
     */
    public function updating(File $file)
    {
        Storage::delete($file->getOriginal('path'));
    }

    /**
     * Handle the file "deleted" event.
     *
     * @param  \App\Models\File  $file
     * @return void
     */
    public function deleted(File $file)
    {
        Storage::delete($file->path);
    }
}
