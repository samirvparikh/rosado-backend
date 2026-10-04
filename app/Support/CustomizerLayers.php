<?php

namespace App\Support;

use App\Models\Bottle;
use App\Models\Cap;
use App\Models\Fragrance;
use Illuminate\Database\Eloquent\Model;

/**
 * Resolves where each customizer layer sits on the 3:4 preview canvas.
 * Positions are canvas percentages (top: % of height; left/width: % of
 * width). A cap or liquid layer uses the bottle's override for that exact
 * combination when the alignment tool saved one, else its own default.
 *
 * The same shape is served to the storefront, frozen into order snapshots,
 * and rendered in admin -- so all three always agree.
 */
class CustomizerLayers
{
    public const CANVAS_ASPECT = '3 / 4';

    public const LABEL_Z = 25;

    /** Validation for a layer's canvas position (slightly off-canvas allowed for bleed). */
    public static function rules(string $prefix = 'layer_'): array
    {
        return [
            "{$prefix}top" => ['required', 'numeric', 'between:-50,150'],
            "{$prefix}left" => ['required', 'numeric', 'between:-50,150'],
            "{$prefix}width" => ['required', 'numeric', 'between:1,200'],
            "{$prefix}z" => ['required', 'integer', 'between:0,99'],
        ];
    }

    public static function labelRules(): array
    {
        return [
            'label_top' => ['required', 'numeric', 'between:0,100'],
            'label_left' => ['required', 'numeric', 'between:0,100'],
            'label_width' => ['required', 'numeric', 'between:5,100'],
        ];
    }

    /** @return array{top: float, left: float, width: float, zIndex: int} */
    public static function box(Model $model): array
    {
        return [
            'top' => (float) $model->layer_top,
            'left' => (float) $model->layer_left,
            'width' => (float) $model->layer_width,
            'zIndex' => (int) $model->layer_z,
        ];
    }

    /** Expects `layerOverrides` loaded on $bottle when overrides should apply. */
    private static function overrideBox(?Bottle $bottle, string $type, string $layerId): ?array
    {
        $override = $bottle?->layerOverrides
            ->first(fn ($row) => $row->layer_type === $type && $row->layer_id === $layerId);

        return $override ? self::box($override) : null;
    }

    public static function bottleLayer(Bottle $bottle): ?array
    {
        return $bottle->image ? ['image' => $bottle->image, ...self::box($bottle)] : null;
    }

    public static function capLayer(Cap $cap, ?Bottle $bottle = null): ?array
    {
        if (! $cap->image) {
            return null;
        }

        return ['image' => $cap->image, ...(self::overrideBox($bottle, 'CAP', $cap->id) ?? self::box($cap))];
    }

    /** Fragrances without a liquid image add no layer (the bottle art shows its own liquid). */
    public static function fragranceLayer(Fragrance $fragrance, ?Bottle $bottle = null): ?array
    {
        if (! $fragrance->liquid_image) {
            return null;
        }

        return ['image' => $fragrance->liquid_image, ...(self::overrideBox($bottle, 'FRAGRANCE', $fragrance->id) ?? self::box($fragrance))];
    }

    /** @return array{top: float, left: float, width: float, zIndex: int} centre-anchored label box */
    public static function labelBox(Bottle $bottle): array
    {
        return [
            'top' => (float) $bottle->label_top,
            'left' => (float) $bottle->label_left,
            'width' => (float) $bottle->label_width,
            'zIndex' => self::LABEL_Z,
        ];
    }

    /**
     * Full stack for one configuration -- what the order freezes and admin renders.
     *
     * @param  list<string>  $labelLines
     */
    public static function compose(Bottle $bottle, Cap $cap, Fragrance $fragrance, string $sizeName, array $labelLines = []): array
    {
        $layers = array_values(array_filter([
            ($layer = self::bottleLayer($bottle)) ? ['type' => 'BOTTLE', ...$layer] : null,
            ($layer = self::fragranceLayer($fragrance, $bottle)) ? ['type' => 'FRAGRANCE', ...$layer] : null,
            ($layer = self::capLayer($cap, $bottle)) ? ['type' => 'CAP', ...$layer] : null,
        ]));

        return [
            'aspect' => self::CANVAS_ASPECT,
            'layers' => $layers,
            'label' => [
                ...self::labelBox($bottle),
                'title' => $fragrance->name,
                'size' => $sizeName,
                'lines' => array_values(array_filter($labelLines, fn ($line) => $line !== null && $line !== '')),
            ],
        ];
    }

    /**
     * Storefront-relative paths (e.g. /images/caps/x.png) live on the
     * storefront host; admin pages run on another host, so absolutise them.
     */
    public static function assetUrl(?string $path): ?string
    {
        if ($path === null || $path === '' || preg_match('#^(https?:)?//#i', $path) || str_starts_with($path, 'data:')) {
            return $path;
        }

        return rtrim((string) config('app.frontend_url'), '/').'/'.ltrim($path, '/');
    }
}
