<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;

class FileUpload
{
    public static function upload($file, $folder, $disk)
    {
        $filename = time() . '.' . $file->getClientOriginalName();
        $path = $file->storePubliclyAs(
            $folder,
            $filename,
            's3'
        );
        $url = Storage::disk($disk)->url($path);

        return response([
            'success' => true,
            'file' => $url,
        ]);
    }
}