<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;

class FileUpload
{
    public static function upload($file, $folder, $disk)
    {

        $file_url = '';
        if ($disk === 's3') {

            $filename = time() . '.' . $file->getClientOriginalName();
            $path = $file->storePubliclyAs(
                $folder,
                $filename,
                $disk
            );
            $file_url = Storage::disk($disk)->url($path);
        }

        return $file_url;
    }
}