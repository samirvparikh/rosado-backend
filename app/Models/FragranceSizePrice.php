<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FragranceSizePrice extends Model
{
    protected $fillable = ['fragrance_id', 'size_id', 'base_price'];

    protected function casts(): array
    {
        return ['base_price' => 'decimal:2'];
    }
}
