<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BottleInventory extends Model
{
    protected $primaryKey = 'bottle_id';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = ['bottle_id', 'current_stock', 'reserved_stock', 'available_stock', 'reorder_level', 'status'];

    protected function casts(): array
    {
        return [
            'current_stock' => 'integer',
            'reserved_stock' => 'integer',
            'available_stock' => 'integer',
            'reorder_level' => 'integer',
        ];
    }

    /** @return BelongsTo<Bottle, $this> */
    public function bottle(): BelongsTo
    {
        return $this->belongsTo(Bottle::class);
    }

    public function reserve(int $quantity): void
    {
        $this->reserved_stock += $quantity;
        $this->available_stock = max(0, $this->current_stock - $this->reserved_stock);
        $this->save();
    }
}
