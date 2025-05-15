<?php

namespace App\Helpers;

class GoogleDriveHelper
{
    public static function getThumbnailUrl($url)
    {
        if (empty($url)) {
            return null;
        }

        $fileId = null;

        // Try to extract id from any format
        if (str_contains($url, 'id=')) {
            parse_str(parse_url($url, PHP_URL_QUERY), $query);
            $fileId = $query['id'] ?? null;
        } elseif (preg_match('/\/d\/(.*?)\//', $url, $matches)) {
            $fileId = $matches[1];
        }

        return $fileId ? "https://drive.google.com/thumbnail?id={$fileId}" : null;
    }
} 