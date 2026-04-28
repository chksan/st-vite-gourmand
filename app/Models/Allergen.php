<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name'])]
class Allergen extends Model
{
    use HasFactory;
    public function plats()
    {
        return $this->belongsToMany(Plat::class, 'plat_allergen');
    }
}