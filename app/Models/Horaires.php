<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['day', 'opening_time', 'closing_time', 'is_closed'])]
class Horaire extends Model
{
    use HasFactory;

    public $timestamps = true;
}