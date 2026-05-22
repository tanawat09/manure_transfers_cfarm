<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class TransferImage
{
    public static function optimizeAndStore(UploadedFile $file, string $prefix): string
    {
        $imageInfo = @getimagesize($file->getRealPath());

        if (! $imageInfo) {
            throw new RuntimeException('ไฟล์รูปภาพไม่ถูกต้อง');
        }

        $rawImage = @file_get_contents($file->getRealPath());
        $sourceImage = $rawImage !== false ? @imagecreatefromstring($rawImage) : false;

        if (! $sourceImage) {
            throw new RuntimeException('ไม่สามารถประมวลผลรูปภาพได้');
        }

        [$width, $height] = $imageInfo;
        $maxDimension = 1600;
        $scale = min($maxDimension / max($width, 1), $maxDimension / max($height, 1), 1);
        $targetWidth = max((int) round($width * $scale), 1);
        $targetHeight = max((int) round($height * $scale), 1);

        $targetImage = imagecreatetruecolor($targetWidth, $targetHeight);

        $mimeType = $imageInfo['mime'] ?? 'image/jpeg';
        $hasTransparency = in_array($mimeType, ['image/png', 'image/webp', 'image/gif'], true);

        if ($hasTransparency) {
            $background = imagecolorallocate($targetImage, 255, 255, 255);
            imagefill($targetImage, 0, 0, $background);
        }

        imagecopyresampled(
            $targetImage,
            $sourceImage,
            0,
            0,
            0,
            0,
            $targetWidth,
            $targetHeight,
            $width,
            $height
        );

        $filename = sprintf(
            'transfers/%s_%s_%s.jpg',
            $prefix,
            time(),
            uniqid()
        );

        ob_start();
        imagejpeg($targetImage, null, 82);
        $binary = ob_get_clean();

        imagedestroy($sourceImage);
        imagedestroy($targetImage);

        if ($binary === false) {
            throw new RuntimeException('ไม่สามารถบันทึกรูปภาพได้');
        }

        Storage::disk('public')->put($filename, $binary);

        return $filename;
    }
}
