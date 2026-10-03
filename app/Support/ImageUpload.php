<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

/**
 * Stores admin-uploaded images straight under public/uploads so they are
 * web-served without a storage:link symlink (not always available on shared
 * hosting), and returns an absolute URL -- the storefront runs on a different
 * origin, so a relative path would resolve against the wrong host.
 */
class ImageUpload
{
    public const RULES = ['image', 'mimes:jpg,jpeg,png,webp,gif,svg', 'max:5120'];

    public static function store(UploadedFile $file, string $folder): string
    {
        $directory = public_path("uploads/{$folder}");
        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $extension = strtolower($file->getClientOriginalExtension() ?: $file->extension() ?: 'jpg');
        $name = now()->format('YmdHis').'-'.Str::lower(Str::random(10)).'.'.$extension;
        $file->move($directory, $name);

        return url("uploads/{$folder}/{$name}");
    }
}
