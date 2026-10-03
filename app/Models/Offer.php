<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** A line of promotional copy shown in the storefront homepage offer marquee. */
class Offer extends Model
{
    protected $fillable = ['text', 'link_url', 'sort_order', 'status'];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }
}
