<?php

namespace Dicicip\FileUpload;

use Intervention\Image\Facades\Image;

class FileUtil
{

    public $rootFilesFolder = '';
    public $relativeFilesFolder = '';

    public function __construct($rootFilesFolder, $relativeFilesFolder)
    {
        echo $rootFilesFolder;
        $this->rootFilesFolder = $rootFilesFolder;
        $this->relativeFilesFolder = $relativeFilesFolder;
    }


    /**
     * Store String Base64 File to Temporary Folder
     *
     * @param string $str64 Fill with a string Base64
     *
     * @return string Temporary File Path
     *
     */
    public function storeBase64ToTemp($strBase64, $thumbQuality = 15)
    {

        /*Is Directory Exist ?*/
        if (!is_dir("{$this->rootFilesFolder}/{$this->relativeFilesFolder}")) {
            mkdir("{$this->rootFilesFolder}/{$this->relativeFilesFolder}", 0777, true);
        }

        if (!is_dir("{$this->rootFilesFolder}/{$this->relativeFilesFolder}/thumb/")) {
            mkdir("{$this->rootFilesFolder}/{$this->relativeFilesFolder}/thumb/", 0777, true);
        }

        /*Is Image File ?*/
        $ext = $this->base64Extension($strBase64);
        $filename = time() . '.' . time() . $ext;

        $path = "{$this->rootFilesFolder}/{$this->relativeFilesFolder}/{$filename}";
        $relativeFilePath = "{$this->relativeFilesFolder}/{$filename}";

        if (strpos($this->getMIMEType($strBase64), 'image') == false) {

            /*file*/
            file_put_contents("{$path}/{$filename}", file_get_contents(base64_decode($strBase64)));

            return new \DicicipFileInfo(
                "{$path}/{$filename}",
                "",
                $relativeFilePath,
                "",
                $filename
            );

        } else {

            /*image*/
            $file = Image::make($strBase64);

            $thumbPath = "{$this->rootFilesFolder}/thumb/{$this->relativeFilesFolder}/{$filename}";

            $file->save($path);
            $file->save($thumbPath, $thumbQuality);

            return new \DicicipFileInfo(
                "{$this->rootFilesFolder}/{$this->relativeFilesFolder}/{$filename}",
                $thumbPath,
                $relativeFilePath,
                "thumb/{$this->relativeFilesFolder}/{$filename}",
                $filename
            );

        }

    }

    /**
     * Store String Base64 File to Temporary Folder
     *
     * @param string $str64 Fill with a string Base64
     *
     * @return string Temporary File Path
     *
     */
    public static function storeTempFileTo($tempFilePath, $targetDirectory)
    {
        if (empty($tempFilePath)) {
            return null;
        }

        if (!is_dir($targetDirectory)) {
            mkdir($targetDirectory, 0777, true);
        }

        if (!is_dir("thumb/{$targetDirectory}")) {
            mkdir("thumb/{$targetDirectory}", 0777, true);
        }

        /*Is Image File ?*/
        $ext = pathinfo($tempFilePath, PATHINFO_EXTENSION);

        if (strpos(mime_content_type($tempFilePath), 'image') == false) {

            /*file*/
            $filename = time() . '.' . time() . $ext;

            $path = "{$targetDirectory}/{$filename}";
            rename($tempFilePath, $path);

            return "{$targetDirectory}/{$filename}";

        } else {

            /*image*/
            $file = Image::make($tempFilePath);

            $filename = time() . '.' . time() . '.' . $ext;

            $path = "{$targetDirectory}/{$filename}";
            $thumbPath = "thumb/{$targetDirectory}/{$filename}";

            $file->save($path);
            $file->save($thumbPath, 15);

            return "{$targetDirectory}/{$filename}";

        }

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

    /**
     * Get File Mime Type From A Base64 String
     *
     * @param string $str64 Fill with a string Base64
     *
     * @return string File mime type
     *
     */
    public static function getMIMEType($base64string): string
    {
        try {
            preg_match("/^data:(.*);base64/", $base64string, $match);
            return $match[1];
        } catch (\Exception $e) {
            return "";
        }
    }

    /**
     * Get File Extension From A Base64 String
     *
     * @param string $str64 Fill with a string Base64
     *
     * @return string File extension
     *
     */
    public static function base64Extension($str64)
    {
        return explode(";", explode("/", $str64)[1])[0];
    }

}