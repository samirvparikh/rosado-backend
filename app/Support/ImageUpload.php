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

    /** Customizer layers (bottle, cap, liquid) must be transparent PNG or WebP. */
    public const LAYER_RULES = ['image', 'mimes:png,webp', 'max:5120'];

    /**
     * Recommended image specs shown on admin upload fields (and checked live
     * in the browser). Pixel size never affects alignment -- positions are
     * canvas percentages -- it only affects sharpness and page weight. The
     * preview canvas is ~500px wide on desktop (~1000px on retina screens);
     * a bottle fills 40-55% of it and a cap 15-20%.
     *
     * @var array<string, array{title: string, minWidth: int, maxWidth: int, maxKb: int, transparent: bool, notes: list<string>}>
     */
    public const SPECS = [
        'bottle' => [
            'title' => 'Bottle image',
            'minWidth' => 600,
            'maxWidth' => 800,
            'maxKb' => 300,
            'transparent' => true,
            'notes' => [
                'Height: whatever the bottle shape needs (bundled bottles are 600 × 630–960 px).',
                'Bottle only, <strong>without the cap</strong> — keep the neck visible at the top.',
                'Crop tight to the glass: no empty transparent margin.',
            ],
        ],
        'cap' => [
            'title' => 'Cap image',
            'minWidth' => 300,
            'maxWidth' => 480,
            'maxKb' => 150,
            'transparent' => true,
            'notes' => [
                'Height: whatever the cap shape needs (bundled caps are 480 × 400–448 px).',
                'Cap only, cropped tight to its edges.',
                'Same lighting and angle as the bottle images so combinations look natural.',
            ],
        ],
        'liquid' => [
            'title' => 'Liquid layer',
            'minWidth' => 600,
            'maxWidth' => 800,
            'maxKb' => 300,
            'transparent' => true,
            'notes' => [
                'Coloured liquid shape only, drawn inside the bottle (between bottle and cap).',
                'Match the width of the bottle images it will sit in.',
            ],
        ],
        'fragrance' => [
            'title' => 'Fragrance image',
            'minWidth' => 600,
            'maxWidth' => 1200,
            'maxKb' => 400,
            'transparent' => false,
            'notes' => [
                'Square image works best (shown as a square tile in the customizer).',
                'Photo (JPG) is fine — no transparency needed.',
            ],
        ],
    ];

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
