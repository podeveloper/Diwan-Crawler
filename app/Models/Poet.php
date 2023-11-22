<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Poet extends Model
{
    use HasFactory;

    protected $fillable = [
        'full_name',
        'nom_de_plume',
        'aka',
        'nationality',
        'birth_year',
        'death_year',
        'birth_place',
        'death_place',
        'biography',
    ];

    public function poems()
    {
        return $this->hasMany(Poem::class);
    }
}
