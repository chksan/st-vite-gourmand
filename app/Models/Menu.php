<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['title', 'description', 'theme', 'regime', 'min_personnes', 'price', 'stock', 'conditions', 'images'])]
class Menu extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'images' => 'array',
        ];
    }

    public function plats(): BelongsToMany
    {
        return $this->belongsToMany(Plat::class);
    }
}