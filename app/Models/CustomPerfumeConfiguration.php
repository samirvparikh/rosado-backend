<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomPerfumeConfiguration extends Model
{
    protected $fillable = [
        'size_id', 'fragrance_id', 'bottle_id', 'cap_id', 'base_price', 'bottle_price',
        'cap_price', 'total_price', 'status', 'custom_name', 'personal_message', 'gift_packaging',
    ];

    protected function casts(): array
    {
        return [
            'base_price' => 'decimal:2',
            'bottle_price' => 'decimal:2',
            'cap_price' => 'decimal:2',
            'total_price' => 'decimal:2',
            'gift_packaging' => 'boolean',
        ];
    }
}
