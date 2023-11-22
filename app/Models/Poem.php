<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Poem extends Model
{
    use HasFactory;

    protected $fillable = ['number_of_poem', 'title', 'couplet_count', 'meter', 'poet_id'];

    public function poet()
    {
        return $this->belongsTo(Poet::class);
    }

    public function couplets()
    {
        return $this->hasMany(Couplet::class);
    }
}
