<?php

namespace Dicicip\FileUpload;

use Intervention\Image\Facades\Image;

class FileUploadUtil {

    public static function storeFileToTemp($filePath, $targetFolder): string
    {
    }

    public static function storeFileWithThumbnail($filePath, $targetFolder): string
    {

        $filePath = "files/{$filePath}";

        if (empty($filePath)) {
            return "";
        }

        if (!is_dir(public_path("files/{$targetFolder}"))) {
            mkdir(public_path("files/{$targetFolder}"), 0777, true);
        }

        if (!is_dir(public_path("files/thumb/{$targetFolder}"))) {
            mkdir(public_path("files/thumb/{$targetFolder}"), 0777, true);
        }

        $ext = pathinfo($filePath, PATHINFO_EXTENSION);

        if (strpos(mime_content_type(public_path($filePath)), 'image') !== false) {

            /*image*/
            $file = Image::make(public_path($filePath));

            $filename = time() . '.' . Str::random(6) . '.' . $ext;

            $path = public_path("files/{$targetFolder}/{$filename}");
            $thumbPath = public_path("files/thumb/{$targetFolder}/{$filename}");

            $file->save($path);
            $file->save($thumbPath, 15);

            return "{$targetFolder}/{$filename}";

        } else {

            /*file*/
            $filename = time() . '.' . Str::random(6) . $ext;

            $path = public_path("{$targetFolder}/{$filename}");
            rename(public_path($filePath), $path);

            return "{$targetFolder}/{$filename}";

        }

    }

    public static function getMIMEType($base64string)
    {
        preg_match("/^data:(.*);base64/", $base64string, $match);
        return $match[1];
    }

    public static function base64Extension($str64)
    {
        return explode(";", explode("/", $str64)[1])[0];
    }

}