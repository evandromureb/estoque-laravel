<?php

declare(strict_types = 1);

namespace App\Support\Http;

use Illuminate\Http\{Request, UploadedFile};

final class RequestUploadedFileList
{
    /**
     * Normalizes a single or multiple uploaded files from the request into a zero-indexed list.
     *
     * @return list<UploadedFile>
     */
    public static function asList(Request $request, string $key): array
    {
        if (!$request->hasFile($key)) {
            return [];
        }

        $files = $request->file($key);

        if ($files instanceof UploadedFile) {
            return [$files];
        }

        if (!is_array($files)) {
            return [];
        }

        return array_values($files);
    }
}
