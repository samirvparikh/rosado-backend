<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** A fragrance, bottle or cap a CUSTOM_PERFUME product offers in its customizer. */
class ProductCustomizerOption extends Model
{
    public const TYPES = ['FRAGRANCE', 'BOTTLE', 'CAP'];

    protected $fillable = ['product_id', 'option_type', 'option_id'];
}
