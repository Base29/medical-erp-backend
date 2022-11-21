<?php

namespace App\Helpers;

use App\Helpers\Response;
use Exception;
use Illuminate\Support\Facades\Storage;

class FileUploadService
{
    public static function upload($file, $folder, $disk)
    {

        try {
            $file_url = '';

            if ($file !== null) {

                if ($disk === 's3') {

                    $filename = time() . '.' . $file->getClientOriginalName();
                    $path = $file->storePubliclyAs(
                        $folder,
                        $filename,
                        $disk
                    );
                    $file_url = Storage::disk($disk)->url($path);
                }
            }

            return $file_url;

        } catch (Exception $e) {

            return Response::fail([
                'code' => 500,
                'message' => $e->getMessage(),
            ]);
        }
    }
}