<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Per-bottle position for a cap or liquid layer (see the alignment tool). */
class CustomizerLayerOverride extends Model
{
    protected $fillable = ['bottle_id', 'layer_type', 'layer_id', 'layer_top', 'layer_left', 'layer_width', 'layer_z'];

    protected function casts(): array
    {
        return [
            'layer_top' => 'float',
            'layer_left' => 'float',
            'layer_width' => 'float',
            'layer_z' => 'integer',
        ];
    }

    /** @return BelongsTo<Bottle, $this> */
    public function bottle(): BelongsTo
    {
        return $this->belongsTo(Bottle::class);
    }
}
