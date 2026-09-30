<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FragranceNote extends Model
{
    protected $fillable = ['fragrance_id', 'note_id', 'note_type', 'sort_order'];

    protected function casts(): array
    {
        return ['sort_order' => 'integer'];
    }

    /** @return BelongsTo<Fragrance, $this> */
    public function fragrance(): BelongsTo
    {
        return $this->belongsTo(Fragrance::class);
    }

    /** @return BelongsTo<Note, $this> */
    public function note(): BelongsTo
    {
        return $this->belongsTo(Note::class);
    }
}
