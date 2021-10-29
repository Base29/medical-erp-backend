<?php

namespace App\Helpers;

use App\Helpers\Response;
use Illuminate\Support\Facades\Storage;

class FileUpload
{
    public static function upload($file, $folder, $disk)
    {

        try {

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

        } catch (\Exception $e) {

            return Response::fail([
                'code' => 500,
                'message' => $e->getMessage(),
            ]);
        }
    }
}