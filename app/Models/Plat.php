<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['type', 'title', 'description'])]
class Plat extends Model
{
    use HasFactory;

    public function allergens()
    {
        return $this->belongsToMany(Allergen::class, 'plat_allergen');
    }

}